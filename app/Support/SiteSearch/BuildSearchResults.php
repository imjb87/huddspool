<?php

namespace App\Support\SiteSearch;

use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Collection;

class BuildSearchResults
{
    private const int RESULT_LIMIT = 8;

    /**
     * @return array<string, array{heading: string, badge: string, route: string, results: Collection}>
     */
    public function build(string $searchTerm): array
    {
        $normalizedSearchTerm = trim($searchTerm);

        if (strlen($normalizedSearchTerm) < 3) {
            return [];
        }

        return collect([
            'players' => [
                'heading' => 'Players',
                'badge' => 'Player',
                'route' => 'player',
                'results' => $this->searchPlayers($normalizedSearchTerm),
            ],
            'teams' => [
                'heading' => 'Teams',
                'badge' => 'Team',
                'route' => 'team',
                'results' => $this->searchTeams($normalizedSearchTerm),
            ],
            'venues' => [
                'heading' => 'Venues',
                'badge' => 'Venue',
                'route' => 'venue',
                'results' => $this->searchVenues($normalizedSearchTerm),
            ],
        ])
            ->filter(fn (array $group): bool => $group['results']->isNotEmpty())
            ->all();
    }

    /**
     * @return array<int, array{key: string, heading: string, badge: string, results: array<int, array<string, mixed>>}>
     */
    public function forApi(string $searchTerm): array
    {
        return collect($this->build($searchTerm))
            ->map(function (array $group, string $key): array {
                return [
                    'key' => $key,
                    'heading' => $group['heading'],
                    'badge' => $group['badge'],
                    'results' => $group['results']->map(function (object $item) use ($group, $key): array {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'href' => route($group['route'].'.show', $item->id),
                            'avatarUrl' => $key === 'players' ? $item->avatar_url : null,
                            'secondaryText' => match ($key) {
                                'players' => $item->team?->name ?? 'No team assigned',
                                'teams' => $item->openSections->first()?->name ?? 'Open section unavailable',
                                'venues' => $item->address,
                                default => null,
                            },
                        ];
                    })->values()->all(),
                ];
            })
            ->values()
            ->all();
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
