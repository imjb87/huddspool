<?php

namespace App\Livewire\Account;

use App\Models\Fixture;
use App\Models\Section;
use App\Models\Team as TeamModel;
use App\Models\User;
use App\Queries\GetTeamKnockoutMatches;
use App\Queries\GetTeamPlayers;
use App\Support\ResultSubmissionPromptResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
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
    public function currentSection(): ?Section
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
            ->whereHas('homeTeam', fn (Builder $query) => $query->notBye())
            ->whereHas('awayTeam', fn (Builder $query) => $query->notBye())
            ->orderBy('fixture_date')
            ->orderBy('id')
            ->get()
            ->map(function (Fixture $fixture) {
                $actionUrl = $this->resultSubmissionPromptResolver()->actionUrlFor($this->user, $fixture);

                return (object) [
                    'fixture' => $fixture,
                    'action_url' => $actionUrl,
                    'action_label' => $actionUrl ? 'Submit result' : null,
                ];
            })
            ->values();
    }

    #[Computed]
    public function resultSubmissionPrompt(): ?object
    {
        $prompt = $this->resultSubmissionPromptResolver()->promptFor($this->user);

        return $prompt ? (object) $prompt : null;
    }

    #[Computed]
    public function teamKnockoutMatches(): Collection
    {
        if (! $this->team) {
            return collect();
        }

        return new GetTeamKnockoutMatches($this->team)();
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

    private function resultSubmissionPromptResolver(): ResultSubmissionPromptResolver
    {
        return app(ResultSubmissionPromptResolver::class);
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
