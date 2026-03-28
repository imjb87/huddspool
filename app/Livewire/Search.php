<?php

namespace App\Livewire;

use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Search extends Component
{
    private const int RESULT_LIMIT = 8;

    public bool $isOpen = false;

    public mixed $searchTerm = '';

    #[On('openSearch')]
    public function openSearch(): void
    {
        $this->isOpen = true;
        $this->searchTerm = '';
    }

    public function closeSearch(): void
    {
        $this->isOpen = false;
        $this->searchTerm = '';
    }

    public function render(): View
    {
        $normalizedSearchTerm = $this->normalizedSearchTerm();

        return view('livewire.search', [
            'resultGroups' => $this->resultGroups,
            'searchTermLength' => strlen($normalizedSearchTerm),
        ]);
    }

    /**
     * @return array<string, array{heading: string, badge: string, route: string, results: Collection}>
     */
    #[Computed]
    public function resultGroups(): array
    {
        $searchTerm = $this->normalizedSearchTerm();

        if (strlen($searchTerm) < 3) {
            return [];
        }

        return collect([
            'players' => [
                'heading' => 'Players',
                'badge' => 'Player',
                'route' => 'player',
                'results' => $this->searchPlayers($searchTerm),
            ],
            'teams' => [
                'heading' => 'Teams',
                'badge' => 'Team',
                'route' => 'team',
                'results' => $this->searchTeams($searchTerm),
            ],
            'venues' => [
                'heading' => 'Venues',
                'badge' => 'Venue',
                'route' => 'venue',
                'results' => $this->searchVenues($searchTerm),
            ],
        ])
            ->filter(fn (array $group): bool => $group['results']->isNotEmpty())
            ->all();
    }

    private function normalizedSearchTerm(): string
    {
        if (! is_string($this->searchTerm)) {
            return '';
        }

        return trim($this->searchTerm);
    }

    private function searchPlayers(string $searchTerm): Collection
    {
        return User::query()
            ->select(['id', 'name', 'team_id', 'avatar_path'])
            ->with('team:id,name')
            ->where('name', 'like', '%'.$searchTerm.'%')
            ->whereHas('team.sections.season', function ($query) {
                $query->where('is_open', 1);
            })
            ->orderBy('name')
            ->limit(self::RESULT_LIMIT)
            ->get();
    }

    private function searchTeams(string $searchTerm): Collection
    {
        return Team::query()
            ->select(['id', 'name', 'shortname', 'folded_at'])
            ->with('openSections:id,name')
            ->where('name', 'like', '%'.$searchTerm.'%')
            ->whereNull('folded_at')
            ->whereHas('sections.season', function ($query) {
                $query->where('is_open', 1);
            })
            ->orderBy('name')
            ->limit(self::RESULT_LIMIT)
            ->get();
    }

    private function searchVenues(string $searchTerm): Collection
    {
        return Venue::query()
            ->select(['id', 'name', 'address'])
            ->where('name', 'like', '%'.$searchTerm.'%')
            ->orderBy('name')
            ->limit(self::RESULT_LIMIT)
            ->get();
    }
}
