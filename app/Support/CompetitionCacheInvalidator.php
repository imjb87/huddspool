<?php

namespace App\Support;

use App\Models\Expulsion;
use App\Models\Frame;
use App\Models\Result;
use App\Models\Season;
use App\Models\Section;
use App\Models\SectionTeam;
use Illuminate\Support\Facades\Cache;
use Spatie\ResponseCache\Facades\ResponseCache;

class CompetitionCacheInvalidator
{
    public function forgetForRulesetContent(?string $slug, ?int $sectionId, ?int $seasonId, ?int $rulesetId): void
    {
        $this->forgetHomeResponseCache();
        $this->forgetRulesetResponseCache($slug);
        $this->forgetKeys([
            'stats:open-season',
            'nav:past-seasons',
            'history:index',
        ]);
        $this->forgetSectionCaches($sectionId);
        $this->forgetSeasonRulesetHistory($seasonId, $rulesetId);
    }

    public function forgetForResult(Result $result): void
    {
        $this->forgetHomeResponseCache();
        $this->forgetTeamSeasonHistories([
            $result->home_team_id,
            $result->away_team_id,
        ]);
        $this->forgetKeys([
            'stats:season-series',
            'stats:season-series-chart',
        ]);

        $result->loadMissing('section', 'fixture');

        $seasonId = $result->fixture?->season_id ?? $result->section?->season_id;
        $rulesetId = $result->fixture?->ruleset_id ?? $result->section?->ruleset_id;

        $this->forgetSeasonHistory($seasonId);
        $this->forgetSeasonRulesetHistory($seasonId, $rulesetId);
    }

    public function forgetForFrame(Frame $frame): void
    {
        $this->forgetHomeResponseCache();
        $this->forgetKeys([
            'stats:open-season',
            'stats:season-series',
            'stats:season-series-chart',
            'history:index',
            'nav:past-seasons',
        ]);

        $frame->loadMissing('result.section', 'result.fixture');

        $sectionId = $frame->result?->section_id ?? $frame->result?->section?->id;
        $seasonId = $frame->result?->fixture?->season_id ?? $frame->result?->section?->season_id;
        $rulesetId = $frame->result?->fixture?->ruleset_id ?? $frame->result?->section?->ruleset_id;

        $this->forgetSectionCaches($sectionId);
        $this->forgetSeasonHistory($seasonId);
        $this->forgetSeasonRulesetHistory($seasonId, $rulesetId);
        $this->forgetPlayerSeasonHistories([
            $frame->home_player_id,
            $frame->away_player_id,
        ]);
        $this->forgetTeamSeasonHistories([
            $frame->result?->home_team_id,
            $frame->result?->away_team_id,
        ]);
    }

    public function forgetForSection(Section $section): void
    {
        $this->forgetHomeResponseCache();
        $this->forgetKeys([
            'history:index',
            'nav:past-seasons',
        ]);
        $this->forgetSectionCaches($section->id);
        $this->forgetSeasonHistory($section->season_id);
        $this->forgetSeasonRulesetHistory($section->season_id, $section->ruleset_id);
    }

    public function forgetForSectionTeam(SectionTeam $sectionTeam): void
    {
        $this->forgetHomeResponseCache();
        if (! $sectionTeam->section_id) {
            return;
        }

        $this->forgetSectionCaches($sectionTeam->section_id);
        $this->forgetKeys([
            'nav:past-seasons',
            'history:index',
        ]);
        $this->forgetTeamSeasonHistories([$sectionTeam->team_id]);

        $section = Section::query()
            ->select('season_id', 'ruleset_id')
            ->find($sectionTeam->section_id);

        $this->forgetSeasonHistory($section?->season_id);
        $this->forgetSeasonRulesetHistory($section?->season_id, $section?->ruleset_id);
    }

    public function forgetForExpulsion(Expulsion $expulsion): void
    {
        $this->forgetHomeResponseCache();
        $this->forgetKeys([
            'stats:open-season',
            'history:index',
            'nav:past-seasons',
        ]);

        if (! $expulsion->season_id) {
            return;
        }

        $this->forgetSeasonHistory($expulsion->season_id);

        $sections = Section::query()
            ->where('season_id', $expulsion->season_id)
            ->get(['id', 'ruleset_id']);

        foreach ($sections as $section) {
            $this->forgetSectionCaches($section->id);
            $this->forgetSeasonRulesetHistory($expulsion->season_id, $section->ruleset_id);
        }
    }

    public function forgetForSeason(Season $season): void
    {
        $this->forgetHomeResponseCache();
        $this->forgetKeys([
            'stats:open-season',
            'stats:season-series',
            'stats:season-series-chart',
            'nav:past-seasons',
            'history:index',
        ]);
        $this->forgetSeasonHistory($season->id);

        $season->loadMissing('sections');

        foreach ($season->sections as $section) {
            $this->forgetSectionCaches($section->id);
            $this->forgetSeasonRulesetHistory($season->id, $section->ruleset_id);
        }
    }

    public function forgetForNews(): void
    {
        $this->forgetHomeResponseCache();
    }

    private function forgetHomeResponseCache(): void
    {
        ResponseCache::forget('/');
    }

    private function forgetRulesetResponseCache(?string $slug): void
    {
        if (blank($slug)) {
            return;
        }

        foreach ($this->rulesetCachePaths($slug) as $path) {
            ResponseCache::forget($path);
        }
    }

    private function forgetSectionCaches(?int $sectionId): void
    {
        if (! $sectionId) {
            return;
        }

        $this->forgetKeys([
            sprintf('section:%d:averages', $sectionId),
            sprintf('section:%d:standings', $sectionId),
        ]);
    }

    private function forgetSeasonHistory(?int $seasonId): void
    {
        if (! $seasonId) {
            return;
        }

        Cache::forget(sprintf('history:season:%d', $seasonId));
    }

    private function forgetSeasonRulesetHistory(?int $seasonId, ?int $rulesetId): void
    {
        if (! $seasonId || ! $rulesetId) {
            return;
        }

        Cache::forget(sprintf('history:sections:%d:%d', $seasonId, $rulesetId));
    }

    /**
     * @param  array<int, int|null>  $playerIds
     */
    private function forgetPlayerSeasonHistories(array $playerIds): void
    {
        foreach ($this->normalizedIds($playerIds) as $playerId) {
            Cache::forget("player:season-history:{$playerId}");
        }
    }

    /**
     * @param  array<int, int|null>  $teamIds
     */
    private function forgetTeamSeasonHistories(array $teamIds): void
    {
        foreach ($this->normalizedIds($teamIds) as $teamId) {
            Cache::forget("team:season-history:{$teamId}");
        }
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

    /**
     * @return array<int, string>
     */
    private function rulesetCachePaths(string $slug): array
    {
        return [
            "/tables/{$slug}/",
            "/fixtures-and-results/{$slug}/",
            "/players/averages/{$slug}/",
        ];
    }
}
