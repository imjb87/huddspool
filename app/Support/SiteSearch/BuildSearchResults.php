<?php

namespace App\Support\SiteSearch;

use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class BuildSearchResults
{
    private const int RESULT_LIMIT = 8;

    private const array SEARCH_OPTIONS = [
        'players' => [
            'query_by_weights' => '12,5,3,1',
            'num_typos' => '2,1,1,1',
            'prefix' => 'true,true,false,false',
            'drop_tokens_threshold' => 1,
            'typo_tokens_threshold' => 1,
            'min_len_1typo' => 4,
            'min_len_2typo' => 8,
            'prioritize_exact_match' => true,
        ],
        'teams' => [
            'query_by_weights' => '12,10,3,2',
            'num_typos' => '2,1,1,1',
            'prefix' => 'true,true,false,false',
            'drop_tokens_threshold' => 1,
            'typo_tokens_threshold' => 1,
            'min_len_1typo' => 4,
            'min_len_2typo' => 8,
            'prioritize_exact_match' => true,
        ],
        'venues' => [
            'query_by_weights' => '12,4',
            'num_typos' => '2,1',
            'prefix' => 'true,false',
            'drop_tokens_threshold' => 1,
            'typo_tokens_threshold' => 1,
            'min_len_1typo' => 4,
            'min_len_2typo' => 8,
            'prioritize_exact_match' => true,
        ],
        'sections' => [
            'query_by_weights' => '12,4,3,2,1',
            'num_typos' => '2,1,1,1,0',
            'prefix' => 'true,false,false,false,false',
            'drop_tokens_threshold' => 1,
            'typo_tokens_threshold' => 1,
            'min_len_1typo' => 4,
            'min_len_2typo' => 8,
            'prioritize_exact_match' => true,
        ],
    ];

    /**
     * @return array<string, array{heading: string, badge: string, route: string, results: Collection}>
     */
    public function build(string $searchTerm): array
    {
        $normalizedSearchTerm = trim($searchTerm);

        if (strlen($normalizedSearchTerm) < 3) {
            return [];
        }

        $results = $this->searchWithTypesense($normalizedSearchTerm)
            ?? $this->searchDatabase($normalizedSearchTerm);

        return collect([
            'players' => [
                'heading' => 'Players',
                'badge' => 'Player',
                'route' => 'player',
                'results' => $results['players'],
            ],
            'teams' => [
                'heading' => 'Teams',
                'badge' => 'Team',
                'route' => 'team',
                'results' => $results['teams'],
            ],
            'venues' => [
                'heading' => 'Venues',
                'badge' => 'Venue',
                'route' => 'venue',
                'results' => $results['venues'],
            ],
            'sections' => [
                'heading' => 'Sections',
                'badge' => 'Section',
                'route' => null,
                'results' => $results['sections'],
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
                $relations = match ($key) {
                    'players' => ['team'],
                    'teams' => ['openSections'],
                    'sections' => ['ruleset', 'season'],
                    default => [],
                };

                $group['results']->loadMissing($relations);

                return [
                    'key' => $key,
                    'heading' => $group['heading'],
                    'badge' => $group['badge'],
                    'results' => $group['results']->map(function (object $item) use ($group, $key): array {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'href' => $key === 'sections'
                                ? route('ruleset.section.show', [
                                    'ruleset' => $item->ruleset,
                                    'section' => $item,
                                ])
                                : route($group['route'].'.show', $item->id),
                            'avatarUrl' => $key === 'players' ? $item->avatar_url : null,
                            'secondaryText' => match ($key) {
                                'players' => $item->team?->name ?? 'No team assigned',
                                'teams' => $item->openSections->first()?->name ?? 'Open section unavailable',
                                'venues' => $item->address,
                                'sections' => collect([
                                    $item->ruleset?->name,
                                    $item->season?->name,
                                ])->filter()->implode(' · '),
                                default => null,
                            },
                        ];
                    })->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<string, Collection>|null
     */
    private function searchWithTypesense(string $searchTerm): ?array
    {
        if (! $this->typesenseIsConfigured()) {
            return null;
        }

        try {
            return [
                'players' => User::search($searchTerm)
                    ->options(self::SEARCH_OPTIONS['players'])
                    ->take(self::RESULT_LIMIT)
                    ->get(),
                'teams' => Team::search($searchTerm)
                    ->options(self::SEARCH_OPTIONS['teams'])
                    ->take(self::RESULT_LIMIT)
                    ->get(),
                'venues' => Venue::search($searchTerm)
                    ->options(self::SEARCH_OPTIONS['venues'])
                    ->take(self::RESULT_LIMIT)
                    ->get(),
                'sections' => Section::search($searchTerm)
                    ->options(self::SEARCH_OPTIONS['sections'])
                    ->take(self::RESULT_LIMIT)
                    ->get(),
            ];
        } catch (Throwable $exception) {
            Log::warning('Typesense site search failed; falling back to database search.', [
                'exception' => $exception::class,
            ]);

            return null;
        }
    }

    private function typesenseIsConfigured(): bool
    {
        return config('scout.driver') === 'typesense'
            && filled(config('scout.typesense.client-settings.api_key'))
            && filled(config('scout.typesense.client-settings.nodes.0.host'));
    }

    /**
     * @return array<string, Collection>
     */
    private function searchDatabase(string $searchTerm): array
    {
        $like = '%'.$searchTerm.'%';

        return [
            'players' => User::query()
                ->select(['id', 'name', 'team_id', 'avatar_path', 'created_at'])
                ->with('team:id,name,shortname')
                ->where(function (Builder $query) use ($like): void {
                    $query->where('name', 'like', $like)
                        ->orWhereHas('team', function (Builder $teamQuery) use ($like): void {
                            $teamQuery->where('name', 'like', $like)
                                ->orWhere('shortname', 'like', $like)
                                ->orWhereHas('openSections', fn (Builder $sectionQuery) => $sectionQuery
                                    ->where('name', 'like', $like));
                        });
                })
                ->whereHas('team', fn (Builder $teamQuery) => $teamQuery
                    ->whereNull('folded_at')
                    ->notBye()
                    ->inOpenSeason())
                ->orderBy('name')
                ->limit(self::RESULT_LIMIT)
                ->get(),
            'teams' => Team::query()
                ->select(['id', 'name', 'shortname', 'venue_id', 'folded_at', 'created_at'])
                ->with(['venue:id,name', 'openSections:id,name'])
                ->where(function (Builder $query) use ($like): void {
                    $query->where('name', 'like', $like)
                        ->orWhere('shortname', 'like', $like)
                        ->orWhereHas('venue', fn (Builder $venueQuery) => $venueQuery
                            ->where('name', 'like', $like))
                        ->orWhereHas('openSections', fn (Builder $sectionQuery) => $sectionQuery
                            ->where('name', 'like', $like));
                })
                ->whereNull('folded_at')
                ->notBye()
                ->inOpenSeason()
                ->orderBy('name')
                ->limit(self::RESULT_LIMIT)
                ->get(),
            'venues' => Venue::query()
                ->select(['id', 'name', 'address', 'created_at'])
                ->where(function (Builder $query) use ($like): void {
                    $query->where('name', 'like', $like)
                        ->orWhere('address', 'like', $like);
                })
                ->orderBy('name')
                ->limit(self::RESULT_LIMIT)
                ->get(),
            'sections' => Section::query()
                ->select(['id', 'name', 'slug', 'ruleset_id', 'season_id', 'created_at'])
                ->with(['ruleset:id,name,slug', 'season:id,name'])
                ->whereHas('season', fn (Builder $seasonQuery) => $seasonQuery
                    ->where('is_open', true))
                ->where(function (Builder $query) use ($like): void {
                    $query->where('name', 'like', $like)
                        ->orWhereHas('ruleset', fn (Builder $rulesetQuery) => $rulesetQuery
                            ->where('name', 'like', $like))
                        ->orWhereHas('season', fn (Builder $seasonQuery) => $seasonQuery
                            ->where('name', 'like', $like))
                        ->orWhereHas('teams', function (Builder $teamQuery) use ($like): void {
                            $teamQuery->where('name', 'like', $like)
                                ->orWhere('shortname', 'like', $like);
                        });
                })
                ->orderBy('name')
                ->limit(self::RESULT_LIMIT)
                ->get(),
        ];
    }
}
