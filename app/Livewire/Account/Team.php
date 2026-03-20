<?php

namespace App\Livewire\Account;

use App\Models\Fixture;
use App\Queries\GetTeamKnockoutMatches;
use App\Support\OrdinalFormatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;

class Team extends BaseAccountComponent
{
    use AuthorizesRequests;

    public function mount(): void
    {
        abort_unless($this->canManageTeam && $this->team, 403);
    }

    #[Computed]
    public function canManageTeam(): bool
    {
        return $this->user->isCaptain() || $this->user->isTeamAdmin();
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
            'label' => app(OrdinalFormatter::class)->for($position).' of '.$standings->count(),
            'points' => (int) ($standing->points ?? 0),
            'played' => (int) ($standing->played ?? 0),
        ];
    }

    public function render(): View
    {
        return view('livewire.account.team');
    }
}
