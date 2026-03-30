<?php

namespace App\Livewire\Team;

use App\Models\Fixture;
use App\Models\Section;
use App\Models\Team;
use App\Support\FixtureSummaryRow;
use App\Support\ResultSubmissionPromptResolver;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class FixturesSection extends Component
{
    public Team $team;

    public ?Section $section = null;

    public bool $forAccount = false;

    public int $page = 1;

    public int $perPage = 5;

    public function mount(Team $team, ?Section $section = null, bool $forAccount = false): void
    {
        $this->team = $team;
        $this->section = $section;
        $this->forAccount = $forAccount;
        $this->page = $this->defaultPage();
    }

    #[Computed]
    public function allFixtures(): Collection
    {
        if (! $this->section) {
            return collect();
        }

        return Fixture::query()
            ->with([
                'homeTeam' => fn ($query) => $query->withTrashed(),
                'awayTeam' => fn ($query) => $query->withTrashed(),
                'result',
            ])
            ->forTeam($this->team)
            ->inOpenSeason()
            ->where('section_id', $this->section->id)
            ->orderBy('week')
            ->orderBy('id')
            ->get();
    }

    #[Computed]
    public function fixtureRows(): Collection
    {
        return $this->allFixtures
            ->forPage($this->page, $this->perPage)
            ->values()
            ->map(function (Fixture $fixture) {
                $actionUrl = null;
                $actionLabel = null;

                if ($this->forAccount && auth()->user()) {
                    $actionUrl = $this->resultSubmissionPromptResolver()->actionUrlFor(auth()->user(), $fixture);
                    $actionLabel = $actionUrl ? 'Submit result' : null;
                }

                return FixtureSummaryRow::fromFixture($fixture, $this->team->id, $actionUrl, $actionLabel);
            });
    }

    public function previousPage(): void
    {
        $this->page = max(1, $this->page - 1);

        unset($this->fixtureRows);
    }

    public function nextPage(): void
    {
        if (! $this->hasNextPage()) {
            return;
        }

        $this->page++;

        unset($this->fixtureRows);
    }

    public function lastPage(): int
    {
        return max(1, (int) ceil($this->allFixtures->count() / $this->perPage));
    }

    public function hasNextPage(): bool
    {
        return $this->page < $this->lastPage();
    }

    public function render(): View
    {
        return view('livewire.team.fixtures-section');
    }

    private function defaultPage(): int
    {
        $fixtures = $this->allFixtures->values();

        if ($fixtures->isEmpty()) {
            return 1;
        }

        $currentWeek = $this->defaultWeek();

        $index = $fixtures->search(fn ($fixture) => (int) $fixture->week === $currentWeek);

        if ($index === false) {
            $index = $fixtures->search(fn ($fixture) => (int) $fixture->week > $currentWeek);
        }

        if ($index === false) {
            return max(1, (int) ceil($fixtures->count() / $this->perPage));
        }

        return (int) floor($index / $this->perPage) + 1;
    }

    private function defaultWeek(): int
    {
        if (! $this->section) {
            return 1;
        }

        foreach ($this->section->season->dates ?? [] as $key => $date) {
            if (date('W', strtotime($date)) === now()->format('W')) {
                return $key + 1;
            }
        }

        return 1;
    }

    private function resultSubmissionPromptResolver(): ResultSubmissionPromptResolver
    {
        return new ResultSubmissionPromptResolver;
    }
}
