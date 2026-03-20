<?php

namespace App\Livewire\Account;

use App\Enums\UserRole;
use App\KnockoutType;
use App\Models\KnockoutMatch;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Queries\GetPlayerAverages;
use App\Queries\GetPlayerFrames;
use App\Queries\GetPlayerSeasonHistory;
use App\Queries\GetTeamPlayers;
use App\Support\ResultSubmissionPromptResolver;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Show extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    public ?TemporaryUploadedFile $avatarUpload = null;

    public ?string $email = null;

    public ?string $telephone = null;

    public bool $removeAvatar = false;

    public function mount(): void
    {
        $this->email = $this->user->email;
        $this->telephone = $this->user->telephone;
    }

    public function saveProfile(): void
    {
        $this->authorize('updateProfile', $this->user);

        $validated = $this->validate([
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user->id)],
            'telephone' => ['nullable', 'string', 'max:255'],
            'avatarUpload' => ['nullable', 'image', 'max:5120'],
        ]);

        $avatarPath = $this->user->avatar_path;

        if ($this->removeAvatar) {
            if ($this->user->avatar_path) {
                Storage::disk('public')->delete($this->user->avatar_path);
            }

            $avatarPath = null;
        } elseif ($this->avatarUpload) {
            $avatarPath = $this->avatarUpload->store('avatars', 'public');

            if ($this->user->avatar_path) {
                Storage::disk('public')->delete($this->user->avatar_path);
            }
        }

        $this->user->update([
            'email' => $validated['email'] ?: null,
            'telephone' => $validated['telephone'] ?: null,
            'avatar_path' => $avatarPath,
        ]);

        $this->avatarUpload = null;
        $this->removeAvatar = false;
        unset($this->user);
    }

    public function clearAvatar(): void
    {
        $this->authorize('updateAvatar', $this->user);

        $this->avatarUpload = null;
        $this->removeAvatar = true;
    }

    public function promoteToTeamAdmin(int $playerId): void
    {
        $member = $this->captainTeamMember($playerId);

        $member->update([
            'role' => UserRole::TeamAdmin->value,
        ]);

        unset($this->user);
        unset($this->teamMembers);

        session()->flash('status', 'Player promoted to team admin');
    }

    public function removeFromTeam(int $playerId): void
    {
        $member = $this->captainTeamMember($playerId);

        if ($member->is($this->user)) {
            abort(403);
        }

        if ($this->team && $this->team->captain_id === $member->id) {
            abort(403);
        }

        $member->update([
            'team_id' => null,
            'role' => UserRole::Player->value,
        ]);

        unset($this->user);
        unset($this->team);
        unset($this->teamMembers);
        unset($this->record);
        unset($this->frames);

        session()->flash('status', 'Player removed from team');
    }

    #[Computed]
    public function user(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->loadMissing([
            'team.venue',
            'team.captain',
        ]);
    }

    #[Computed]
    public function team(): ?Team
    {
        return $this->user->team;
    }

    #[Computed]
    public function currentSection(): ?Section
    {
        return $this->team?->openSection();
    }

    #[Computed]
    public function record(): object
    {
        return new GetPlayerAverages($this->user, $this->currentSection)();
    }

    #[Computed]
    public function frames(): Collection
    {
        return new GetPlayerFrames($this->user, $this->currentSection)();
    }

    #[Computed]
    public function history(): Collection
    {
        return new GetPlayerSeasonHistory($this->user)();
    }

    #[Computed]
    public function knockoutMatches(): Collection
    {
        $player = $this->user;

        $participantQuery = function ($query) use ($player) {
            $query->where('player_one_id', $player->id)
                ->orWhere('player_two_id', $player->id);

            if ($player->team_id) {
                $query->orWhere('team_id', $player->team_id);
            }
        };

        return KnockoutMatch::query()
            ->with([
                'round.knockout',
                'homeParticipant',
                'awayParticipant',
                'venue',
                'forfeitParticipant',
                'winner',
            ])
            ->whereHas('round', fn ($query) => $query->where('is_visible', true))
            ->whereHas('round.knockout', fn ($query) => $query->whereIn('type', [
                KnockoutType::Singles,
                KnockoutType::Doubles,
            ]))
            ->where(function ($query) use ($participantQuery) {
                $query->whereHas('homeParticipant', $participantQuery)
                    ->orWhereHas('awayParticipant', $participantQuery);
            })
            ->orderByDesc('starts_at')
            ->orderByDesc('id')
            ->get()
            ->values();
    }

    #[Computed]
    public function resultSubmissionPrompt(): ?object
    {
        $prompt = $this->resultSubmissionPromptResolver()->promptFor($this->user);

        return $prompt ? (object) $prompt : null;
    }

    #[Computed]
    public function teamMembers(): Collection
    {
        if (! $this->team) {
            return collect();
        }

        return new GetTeamPlayers($this->team, $this->currentSection)();
    }

    public function render(): View
    {
        return view('livewire.account.show');
    }

    private function resultSubmissionPromptResolver(): ResultSubmissionPromptResolver
    {
        return app(ResultSubmissionPromptResolver::class);
    }

    private function captainTeamMember(int $playerId): User
    {
        if (! $this->user->isCaptain() || ! $this->team) {
            abort(403);
        }

        return $this->team->players()->whereKey($playerId)->firstOrFail();
    }
}
