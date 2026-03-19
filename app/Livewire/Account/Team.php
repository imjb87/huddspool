<?php

namespace App\Livewire\Account;

use App\Enums\UserRole;
use App\KnockoutType;
use App\Models\Fixture;
use App\Models\KnockoutMatch;
use App\Models\Result;
use App\Models\Team as TeamModel;
use App\Models\User;
use App\Queries\GetTeamPlayers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Team extends Component
{
    use AuthorizesRequests;

    public function mount(): void
    {
        abort_unless($this->canManageTeam && $this->team, 403);
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
    public function team(): ?TeamModel
    {
        return $this->user->team;
    }

    #[Computed]
    public function currentSection()
    {
        return $this->team?->openSection();
    }

    #[Computed]
    public function canManageTeam(): bool
    {
        return $this->user->isCaptain() || $this->user->isTeamAdmin();
    }

    #[Computed]
    public function teamMembers(): Collection
    {
        if (! $this->team) {
            return collect();
        }

        return new GetTeamPlayers($this->team, $this->currentSection)();
    }

    #[Computed]
    public function fixtures(): Collection
    {
        if (! $this->team) {
            return collect();
        }

        return Fixture::query()
            ->with(['result', 'homeTeam', 'awayTeam'])
            ->inOpenSeason()
            ->forTeam($this->team)
            ->whereHas('homeTeam', fn ($query) => $query->where('name', '!=', 'Bye'))
            ->whereHas('awayTeam', fn ($query) => $query->where('name', '!=', 'Bye'))
            ->orderBy('fixture_date')
            ->orderBy('id')
            ->get()
            ->map(function (Fixture $fixture) {
                $isDue = $fixture->fixture_date?->isPast() || $fixture->fixture_date?->isToday();
                $actionUrl = null;
                $actionLabel = null;

                if ($isDue) {
                    if ($fixture->result instanceof Result && ! $fixture->result->is_confirmed && Gate::allows('resumeSubmission', $fixture->result)) {
                        $actionUrl = route('result.create', $fixture);
                        $actionLabel = 'Submit result';
                    } elseif (! $fixture->result && Gate::allows('createResult', $fixture)) {
                        $actionUrl = route('result.create', $fixture);
                        $actionLabel = 'Submit result';
                    }
                }

                return (object) [
                    'fixture' => $fixture,
                    'action_url' => $actionUrl,
                    'action_label' => $actionLabel,
                ];
            })
            ->values();
    }

    #[Computed]
    public function resultSubmissionPrompt(): ?object
    {
        $fixture = $this->fixtures->first(fn ($item) => $item->action_url);

        if (! $fixture) {
            return null;
        }

        return (object) [
            'message' => 'A team result is ready to submit.',
            'fixture_label' => sprintf(
                '%s vs %s',
                $fixture->fixture->homeTeam?->name ?? 'TBC',
                $fixture->fixture->awayTeam?->name ?? 'TBC',
            ),
            'url' => $fixture->action_url,
        ];
    }

    #[Computed]
    public function teamKnockoutMatches(): Collection
    {
        if (! $this->team) {
            return collect();
        }

        return KnockoutMatch::query()
            ->with([
                'round.knockout',
                'homeParticipant',
                'awayParticipant',
                'winner',
            ])
            ->whereHas('round', fn ($query) => $query->where('is_visible', true))
            ->whereHas('round.knockout', fn ($query) => $query->where('type', KnockoutType::Team))
            ->where(function ($query) {
                $query->whereHas('homeParticipant', fn ($participantQuery) => $participantQuery->where('team_id', $this->team->id))
                    ->orWhereHas('awayParticipant', fn ($participantQuery) => $participantQuery->where('team_id', $this->team->id));
            })
            ->orderByDesc('starts_at')
            ->orderByDesc('id')
            ->get()
            ->values();
    }

    #[Computed]
    public function currentStanding(): ?object
    {
        if (! $this->team || ! $this->currentSection) {
            return null;
        }

        $standings = $this->currentSection->standings()->values();
        $index = $standings->search(fn ($standing) => (int) $standing->id === (int) $this->team->id);

        if ($index === false) {
            return null;
        }

        $position = $index + 1;
        $standing = $standings->get($index);

        return (object) [
            'position' => $position,
            'label' => $this->ordinal($position).' of '.$standings->count(),
            'points' => (int) ($standing->points ?? 0),
            'played' => (int) ($standing->played ?? 0),
        ];
    }

    public function render(): View
    {
        return view('livewire.account.team');
    }

    private function captainTeamMember(int $playerId): User
    {
        if (! $this->user->isCaptain() || ! $this->team) {
            abort(403);
        }

        return $this->team->players()->whereKey($playerId)->firstOrFail();
    }

    private function ordinal(int $number): string
    {
        $suffix = match (true) {
            $number % 100 >= 11 && $number % 100 <= 13 => 'th',
            $number % 10 === 1 => 'st',
            $number % 10 === 2 => 'nd',
            $number % 10 === 3 => 'rd',
            default => 'th',
        };

        return Str::of($number)->append($suffix)->toString();
    }
}
