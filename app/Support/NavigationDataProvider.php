<?php

namespace App\Support;

use App\Models\Knockout;
use App\Models\Ruleset;
use App\Models\Season;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class NavigationDataProvider
{
    private const RULESETS_CACHE_KEY = 'nav:rulesets:v4';

    private const NAVIGATION_RULESETS_CACHE_KEY = 'nav:prepared-rulesets:v1';

    private const ACTIVE_KNOCKOUTS_CACHE_KEY = 'nav:active-knockouts:v2';

    private const NAVIGABLE_ACTIVE_KNOCKOUTS_CACHE_KEY = 'nav:navigable-active-knockouts:v1';

    private const HISTORY_SEASON_GROUPS_CACHE_KEY = 'nav:history-season-groups:v1';

    protected function rulesets(): Collection|array
    {
        if (! Schema::hasTable('rulesets')) {
            return [];
        }

        return Cache::remember(self::RULESETS_CACHE_KEY, now()->addMinutes(10), function () {
            return Ruleset::query()
                ->whereHas('sections', fn ($query) => $query
                    ->whereNotNull('slug')
                    ->where('slug', '!=', '')
                    ->whereHas('season', fn ($seasonQuery) => $seasonQuery->where('is_open', true)))
                ->with([
                    'openSections' => fn ($query) => $query
                        ->whereNotNull('slug')
                        ->where('slug', '!=', '')
                        ->with('season'),
                ])
                ->orderBy('id')
                ->get();
        });
    }

    public function pastSeasons(): Collection|array
    {
        if (! Schema::hasTable('seasons')) {
            return [];
        }

        return Cache::remember('nav:past-seasons', now()->addMinutes(10), function () {
            return Season::query()
                ->where('is_open', false)
                ->with([
                    'sections.ruleset:id,name,slug',
                    'knockouts' => fn ($query) => $query->orderBy('name'),
                ])
                ->orderByDesc('id')
                ->get();
        });
    }

    public function activeKnockouts(): Collection
    {
        if (! Schema::hasTable('knockouts')) {
            return collect();
        }

        return Cache::remember(self::ACTIVE_KNOCKOUTS_CACHE_KEY, now()->addMinutes(10), function () {
            return Knockout::query()
                ->whereNotNull('slug')
                ->where('slug', '!=', '')
                ->whereHas('season', fn ($query) => $query->where('is_open', true))
                ->orderByDesc('season_id')
                ->orderBy('name')
                ->get(['id', 'name', 'slug']);
        });
    }

    public function navigationRulesets(): Collection
    {
        return Cache::remember(self::NAVIGATION_RULESETS_CACHE_KEY, now()->addMinutes(10), function () {
            return collect($this->rulesets())
                ->map(function (Ruleset $ruleset): array {
                    return [
                        'id' => $ruleset->id,
                        'name' => $ruleset->name,
                        'ruleset' => $ruleset,
                        'sections' => $ruleset->openSections
                            ->filter(fn ($section) => filled($section?->getRouteKey()))
                            ->values(),
                    ];
                })
                ->filter(fn (array $item) => $item['sections']->isNotEmpty())
                ->values();
        });
    }

    public function navigableActiveKnockouts(): Collection
    {
        return Cache::remember(self::NAVIGABLE_ACTIVE_KNOCKOUTS_CACHE_KEY, now()->addMinutes(10), function () {
            return $this->activeKnockouts()
                ->filter(fn ($knockout) => filled($knockout?->slug))
                ->values();
        });
    }

    public function historySeasonGroups(): Collection
    {
        if (! Schema::hasTable('seasons')) {
            return collect();
        }

        $historyRulesetOrder = [
            'international-rules' => 0,
            'blackball-rules' => 1,
            'epa-rules' => 2,
        ];

        return Cache::remember(self::HISTORY_SEASON_GROUPS_CACHE_KEY, now()->addMinutes(10), function () use ($historyRulesetOrder) {
            return collect($this->pastSeasons())
                ->filter(fn ($season) => $season?->hasConcluded())
                ->values()
                ->map(function ($season) use ($historyRulesetOrder): array {
                    $rulesets = $season->sections
                        ->filter(fn ($section) => $section->ruleset && filled($section->slug))
                        ->groupBy('ruleset_id')
                        ->map(function ($sections) use ($historyRulesetOrder): array {
                            $firstSection = $sections->first();

                            return [
                                'ruleset' => $firstSection->ruleset,
                                'sort_order' => $historyRulesetOrder[$firstSection->ruleset->slug ?? ''] ?? PHP_INT_MAX,
                                'sections' => $sections
                                    ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                                    ->values(),
                            ];
                        })
                        ->sortBy(fn (array $group) => sprintf('%03d-%s', $group['sort_order'], $group['ruleset']->name))
                        ->values();

                    return [
                        'season' => $season,
                        'rulesets' => $rulesets,
                        'knockouts' => $season->knockouts
                            ->filter(fn ($knockout) => filled($knockout?->slug))
                            ->values(),
                    ];
                });
        });
    }
}
