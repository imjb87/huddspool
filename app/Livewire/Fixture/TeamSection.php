<?php

namespace App\Livewire\Fixture;

use App\Models\Section;
use App\Models\Team;
use App\Queries\GetTeamPlayers;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TeamSection extends Component
{
    public Team $team;

    public Section $section;

    public string $title;

    public string $sectionKey;

    public string $side;

    public int $page = 1;

    public int $perPage = 5;

    public function mount(Team $team, Section $section, string $title, string $sectionKey, string $side): void
    {
        $this->team = $team;
        $this->section = $section;
        $this->title = $title;
        $this->sectionKey = $sectionKey;
        $this->side = $side;
    }

    #[Computed]
    public function allPlayers(): Collection
    {
        return (new GetTeamPlayers($this->team, $this->section))();
    }

    #[Computed]
    public function players(): Collection
    {
        return $this->allPlayers
            ->forPage($this->page, $this->perPage)
            ->values();
    }

    public function previousPage(): void
    {
        $this->page = max(1, $this->page - 1);

        unset($this->players);
    }

    public function nextPage(): void
    {
        if (! $this->hasNextPage()) {
            return;
        }

        $this->page++;

        unset($this->players);
    }

    public function hasNextPage(): bool
    {
        return $this->page < $this->lastPage();
    }

    public function lastPage(): int
    {
        return max(1, (int) ceil($this->allPlayers->count() / $this->perPage));
    }

    public function render(): View
    {
        return view('livewire.fixture.team-section');
    }
}
