<?php

namespace App\Livewire\Team;

use App\Models\Section;
use App\Models\Team;
use App\Queries\GetTeamPlayers;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class PlayersSection extends Component
{
    use WithoutUrlPagination;
    use WithPagination;

    public Team $team;

    public ?Section $section = null;

    public bool $forAccount = false;

    public int $perPage = 5;

    public function mount(Team $team, ?Section $section = null, bool $forAccount = false): void
    {
        $this->team = $team;
        $this->section = $section;
        $this->forAccount = $forAccount;
    }

    #[Computed]
    public function allPlayers(): Collection
    {
        return (new GetTeamPlayers($this->team, $this->section))();
    }

    #[Computed]
    public function players(): LengthAwarePaginator
    {
        $page = $this->getPage();
        $players = $this->allPlayers;

        return new Paginator(
            items: $players->forPage($page, $this->perPage)->values(),
            total: $players->count(),
            perPage: $this->perPage,
            currentPage: $page,
            options: [
                'path' => request()->url(),
                'pageName' => 'page',
            ],
        );
    }

    public function render(): View
    {
        return view('livewire.team.players-section');
    }
}
