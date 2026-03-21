<?php

namespace App\Support;

use App\Models\Section;
use Illuminate\Support\Facades\Cache;

class CompetitionDataCacheInvalidator
{
    /**
     * @var array<int, string>
     */
    private const NAVIGATION_CACHE_KEYS = [
        'nav:past-seasons',
        'history:index',
    ];

    /**
     * @var array<int, string>
     */
    private const OPEN_SEASON_STATS_CACHE_KEYS = [
        'stats:open-season',
    ];

    /**
     * @var array<int, string>
     */
    private const SEASON_SERIES_CACHE_KEYS = [
        'stats:season-series',
        'stats:season-series-chart',
    ];

    public function forgetNavigationCaches(): void
    {
        $this->forgetKeys(self::NAVIGATION_CACHE_KEYS);
    }

    public function forgetOpenSeasonStats(): void
    {
        $this->forgetKeys(self::OPEN_SEASON_STATS_CACHE_KEYS);
    }

    public function forgetSeasonSeriesCaches(): void
    {
        $this->forgetKeys(self::SEASON_SERIES_CACHE_KEYS);
    }

    public function forgetPastSeasonNavigation(): void
    {
        $this->forgetKeys(['nav:past-seasons']);
    }

    public function forgetSectionRelatedCaches(?int $sectionId, ?int $seasonId, ?int $rulesetId): void
    {
        $this->forgetSectionCaches($sectionId);
        $this->forgetSeasonRelatedCaches($seasonId, $rulesetId);
    }

    public function forgetSectionCaches(?int $sectionId): void
    {
        if (! $sectionId) {
            return;
        }

        $this->forgetKeys([
            sprintf('section:%d:averages', $sectionId),
            sprintf('section:%d:standings', $sectionId),
        ]);
    }

    public function forgetSeasonRelatedCaches(?int $seasonId, ?int $rulesetId): void
    {
        $this->forgetSeasonHistory($seasonId);
        $this->forgetSeasonRulesetHistory($seasonId, $rulesetId);
    }

    public function forgetSeasonHistory(?int $seasonId): void
    {
        if (! $seasonId) {
            return;
        }

        Cache::forget(sprintf('history:season:%d', $seasonId));
    }

    public function forgetSections(iterable $sections): void
    {
        foreach ($sections as $section) {
            $this->forgetSectionRelatedCaches($section->id, $section->season_id, $section->ruleset_id);
        }
    }

    /**
     * @param  array<int, int|null>  $playerIds
     */
    public function forgetPlayerSeasonHistories(array $playerIds): void
    {
        foreach ($this->normalizedIds($playerIds) as $playerId) {
            Cache::forget("player:season-history:{$playerId}");
        }
    }

    /**
     * @param  array<int, int|null>  $teamIds
     */
    public function forgetTeamSeasonHistories(array $teamIds): void
    {
        foreach ($this->normalizedIds($teamIds) as $teamId) {
            Cache::forget("team:season-history:{$teamId}");
        }
    }

    /**
     * @param  array<int, int>  $teamIds
     */
    public function sectionsForTeamIds(array $teamIds)
    {
        return Section::withTrashed()
            ->whereHas('teams', function ($query) use ($teamIds) {
                $query->withTrashed()->whereIn('teams.id', $teamIds);
            })
            ->get(['id', 'season_id', 'ruleset_id']);
    }

    public function sectionsForSeasonId(int $seasonId)
    {
        return Section::query()
            ->where('season_id', $seasonId)
            ->get(['id', 'season_id', 'ruleset_id']);
    }

    /**
     * @param  array<int, string|null>  $keys
     */
    private function forgetKeys(array $keys): void
    {
        foreach (array_filter($keys) as $key) {
            Cache::forget($key);
        }
    }

    private function forgetSeasonRulesetHistory(?int $seasonId, ?int $rulesetId): void
    {
        if (! $seasonId || ! $rulesetId) {
            return;
        }

        Cache::forget(sprintf('history:sections:%d:%d', $seasonId, $rulesetId));
    }

    /**
     * @param  array<int, int|null>  $ids
     * @return array<int, int>
     */
    private function normalizedIds(array $ids): array
    {
        return collect($ids)
            ->filter(fn (?int $id): bool => ! is_null($id))
            ->map(fn (int $id): int => $id)
            ->unique()
            ->values()
            ->all();
    }
}
