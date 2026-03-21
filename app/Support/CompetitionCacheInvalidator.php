<?php

namespace App\Support;

use App\Models\Expulsion;
use App\Models\Frame;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\Page;
use App\Models\Result;
use App\Models\Season;
use App\Models\Section;
use App\Models\SectionTeam;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;
use Spatie\ResponseCache\Facades\ResponseCache;

class CompetitionCacheInvalidator
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

    public function forgetForRulesetContent(?string $slug, ?int $sectionId, ?int $seasonId, ?int $rulesetId): void
    {
        $this->clearResponseCacheTags(ResponseCacheTagSet::rulesetContent(), $this->fallbackRulesetPaths($slug));
        $this->forgetKeys(self::OPEN_SEASON_STATS_CACHE_KEYS);
        $this->forgetKeys(self::NAVIGATION_CACHE_KEYS);
        $this->forgetSectionRelatedCaches($sectionId, $seasonId, $rulesetId);
    }

    public function forgetForResult(Result $result): void
    {
        $this->clearResponseCacheTags(ResponseCacheTagSet::resultContent(), ['/']);
        $this->forgetTeamSeasonHistories([
            $result->home_team_id,
            $result->away_team_id,
        ]);
        $this->forgetKeys(self::SEASON_SERIES_CACHE_KEYS);

        $result->loadMissing('section', 'fixture');

        $seasonId = $result->fixture?->season_id ?? $result->section?->season_id;
        $rulesetId = $result->fixture?->ruleset_id ?? $result->section?->ruleset_id;

        $this->forgetSeasonRelatedCaches($seasonId, $rulesetId);
    }

    public function forgetForFrame(Frame $frame): void
    {
        $this->clearResponseCacheTags(ResponseCacheTagSet::frameContent(), ['/']);
        $this->forgetKeys(self::OPEN_SEASON_STATS_CACHE_KEYS);
        $this->forgetKeys(self::SEASON_SERIES_CACHE_KEYS);
        $this->forgetKeys(self::NAVIGATION_CACHE_KEYS);

        $frame->loadMissing('result.section', 'result.fixture');

        $sectionId = $frame->result?->section_id ?? $frame->result?->section?->id;
        $seasonId = $frame->result?->fixture?->season_id ?? $frame->result?->section?->season_id;
        $rulesetId = $frame->result?->fixture?->ruleset_id ?? $frame->result?->section?->ruleset_id;

        $this->forgetSectionRelatedCaches($sectionId, $seasonId, $rulesetId);
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
        $this->clearResponseCacheTags(ResponseCacheTagSet::sectionContent(), ['/']);
        $this->forgetKeys(self::NAVIGATION_CACHE_KEYS);
        $this->forgetSectionRelatedCaches($section->id, $section->season_id, $section->ruleset_id);
    }

    public function forgetForSectionTeam(SectionTeam $sectionTeam): void
    {
        $this->clearResponseCacheTags(ResponseCacheTagSet::sectionContent(), ['/']);
        if (! $sectionTeam->section_id) {
            return;
        }

        $this->forgetSectionCaches($sectionTeam->section_id);
        $this->forgetKeys(self::NAVIGATION_CACHE_KEYS);
        $this->forgetTeamSeasonHistories([$sectionTeam->team_id]);

        $section = Section::query()
            ->select('season_id', 'ruleset_id')
            ->find($sectionTeam->section_id);

        $this->forgetSeasonRelatedCaches($section?->season_id, $section?->ruleset_id);
    }

    public function forgetForExpulsion(Expulsion $expulsion): void
    {
        $this->clearResponseCacheTags(ResponseCacheTagSet::expulsionContent(), ['/']);
        $this->forgetKeys(self::OPEN_SEASON_STATS_CACHE_KEYS);
        $this->forgetKeys(self::NAVIGATION_CACHE_KEYS);

        if (! $expulsion->season_id) {
            return;
        }

        $this->forgetSeasonHistory($expulsion->season_id);
        $this->forgetSections($this->sectionsForSeasonId($expulsion->season_id));
    }

    public function forgetForSeason(Season $season): void
    {
        $this->clearResponseCacheTags(ResponseCacheTagSet::seasonContent(), ['/']);
        $this->forgetKeys(self::OPEN_SEASON_STATS_CACHE_KEYS);
        $this->forgetKeys(self::SEASON_SERIES_CACHE_KEYS);
        $this->forgetKeys(self::NAVIGATION_CACHE_KEYS);
        $this->forgetSeasonHistory($season->id);

        $season->loadMissing('sections');
        $this->forgetSections($season->sections);
    }

    public function forgetForTeam(Team $team): void
    {
        $this->clearResponseCacheTags(ResponseCacheTagSet::teamContent(), ['/']);
        $this->forgetKeys(self::OPEN_SEASON_STATS_CACHE_KEYS);
        $this->forgetKeys(['nav:past-seasons']);
        $this->forgetTeamSeasonHistories([$team->id]);
        $this->forgetSections($this->sectionsForTeamIds([$team->id]));
    }

    public function forgetForUser(User $user): void
    {
        $this->clearResponseCacheTags(ResponseCacheTagSet::userContent(), ['/']);
        $this->forgetKeys(self::OPEN_SEASON_STATS_CACHE_KEYS);
        $this->forgetPlayerSeasonHistories([$user->id]);

        $teamIds = $this->normalizedIds([
            $user->team_id,
            $user->getOriginal('team_id'),
        ]);

        if ($teamIds === []) {
            return;
        }
        $this->forgetSections($this->sectionsForTeamIds($teamIds));
    }

    public function forgetForNews(): void
    {
        $this->clearResponseCacheTags([ResponseCacheTags::HOME], ['/']);
    }

    public function forgetForVenue(Venue $venue): void
    {
        $this->clearResponseCacheTags(ResponseCacheTagSet::venueContent(), ['/']);
    }

    public function forgetForKnockout(Knockout $knockout): void
    {
        $this->clearResponseCacheTags(ResponseCacheTagSet::knockoutContent(), ['/']);
    }

    public function forgetForKnockoutMatch(KnockoutMatch $match): void
    {
        $this->clearResponseCacheTags(ResponseCacheTagSet::knockoutContent(), ['/']);
    }

    public function forgetForPage(Page $page): void
    {
        $this->clearResponseCacheTags([
            ResponseCacheTags::PAGES,
        ], ['/']);
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

    private function forgetSeasonRelatedCaches(?int $seasonId, ?int $rulesetId): void
    {
        $this->forgetSeasonHistory($seasonId);
        $this->forgetSeasonRulesetHistory($seasonId, $rulesetId);
    }

    private function forgetSectionRelatedCaches(?int $sectionId, ?int $seasonId, ?int $rulesetId): void
    {
        $this->forgetSectionCaches($sectionId);
        $this->forgetSeasonRelatedCaches($seasonId, $rulesetId);
    }

    private function forgetSections(iterable $sections): void
    {
        foreach ($sections as $section) {
            $this->forgetSectionRelatedCaches($section->id, $section->season_id, $section->ruleset_id);
        }
    }

    /**
     * @param  array<int, int>  $teamIds
     */
    private function sectionsForTeamIds(array $teamIds)
    {
        return Section::withTrashed()
            ->whereHas('teams', function ($query) use ($teamIds) {
                $query->withTrashed()->whereIn('teams.id', $teamIds);
            })
            ->get(['id', 'season_id', 'ruleset_id']);
    }

    private function sectionsForSeasonId(int $seasonId)
    {
        return Section::query()
            ->where('season_id', $seasonId)
            ->get(['id', 'season_id', 'ruleset_id']);
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
    private function fallbackRulesetPaths(?string $slug): array
    {
        if (blank($slug)) {
            return ['/'];
        }

        return [
            '/',
            "/rulesets/{$slug}",
            "/tables/{$slug}/",
            "/fixtures-and-results/{$slug}/",
            "/players/averages/{$slug}/",
        ];
    }

    /**
     * @param  array<int, string>  $tags
     * @param  array<int, string>  $fallbackPaths
     */
    private function clearResponseCacheTags(array $tags, array $fallbackPaths = ['/']): void
    {
        if ($this->supportsTaggedResponseCache()) {
            ResponseCache::clear($tags);

            return;
        }

        ResponseCache::forget($fallbackPaths);
    }

    protected function supportsTaggedResponseCache(): bool
    {
        $store = Cache::store(config('responsecache.cache.store'))->getStore();

        return $store instanceof TaggableStore && filled(config('responsecache.cache.tag'));
    }
}
