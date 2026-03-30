<?php

namespace App\Livewire\Team;

use App\Models\Section;
use App\Models\Team;
use App\Queries\GetTeamSeasonHistory;
use App\Support\TeamHistoryRow;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class HistorySection extends Component
{
    public Team $team;

    public ?Section $currentSection = null;

    public int $page = 1;

    public int $perPage = 5;

    public function mount(Team $team, ?Section $currentSection = null): void
    {
        $this->team = $team;
        $this->currentSection = $currentSection;
    }

    #[Computed]
    public function allHistory(): Collection
    {
        $entries = (new GetTeamSeasonHistory($this->team))()
            ->filter(fn (array $entry): bool => $entry['season_id'] !== $this->currentSection?->season_id)
            ->reject(fn (array $entry): bool => $entry['team_expelled'] ?? false)
            ->values();

        $sections = Section::query()
            ->withStandingsRelations()
            ->whereIn('id', $entries->pluck('section_id')->filter()->unique())
            ->get()
            ->keyBy('id');

        return $entries->map(function (array $entry) use ($sections) {
            $position = null;

            if ($entry['section_id'] && $sections->has($entry['section_id'])) {
                $standingIndex = $sections[$entry['section_id']]
                    ->standings()
                    ->search(fn ($standing) => (int) $standing->id === (int) $this->team->id);

                $position = $standingIndex === false ? null : $standingIndex + 1;
            }

            return TeamHistoryRow::fromEntry($entry, $position);
        });
    }

    #[Computed]
    public function historyRows(): Collection
    {
        return $this->allHistory
            ->forPage($this->page, $this->perPage)
            ->values();
    }

    public function previousPage(): void
    {
        $this->page = max(1, $this->page - 1);

        unset($this->historyRows);
    }

    public function nextPage(): void
    {
        if (! $this->hasNextPage()) {
            return;
        }

        $this->page++;

        unset($this->historyRows);
    }

    public function hasNextPage(): bool
    {
        return $this->page < $this->lastPage();
    }

    public function lastPage(): int
    {
        return max(1, (int) ceil($this->allHistory->count() / $this->perPage));
    }

    public function render(): View
    {
        return view('livewire.team.history-section');
    }
}
