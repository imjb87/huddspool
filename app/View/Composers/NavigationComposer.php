<?php

namespace App\View\Composers;

use App\Models\Knockout;
use App\Models\Ruleset;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use STS\FilamentImpersonate\Facades\Impersonation;

class NavigationComposer
{
    private const RULESETS_CACHE_KEY = 'nav:rulesets:v4';

    private const NAVIGATION_RULESETS_CACHE_KEY = 'nav:prepared-rulesets:v1';

    private const ACTIVE_KNOCKOUTS_CACHE_KEY = 'nav:active-knockouts:v2';

    private const NAVIGABLE_ACTIVE_KNOCKOUTS_CACHE_KEY = 'nav:navigable-active-knockouts:v1';

    private const HISTORY_SEASON_GROUPS_CACHE_KEY = 'nav:history-season-groups:v1';

    public function compose(View $view): void
    {
        $view->with([
            'rulesets' => $this->rulesets(),
            'past_seasons' => $this->pastSeasons(),
            'active_knockouts' => $this->activeKnockouts(),
            'navigationRulesets' => $this->navigationRulesets(),
            'historySeasonGroups' => $this->historySeasonGroups(),
            'navigableKnockouts' => $this->navigableActiveKnockouts(),
            'is_impersonating' => Impersonation::isImpersonating(),
        ] + $this->navigationViewData(request()));
    }

    /**
     * @return array{
     *     currentRuleset: mixed,
     *     currentPage: mixed,
     *     isRulesetRoute: bool,
     *     isKnockoutRoute: bool,
     *     mobileDrawerPanelClasses: string,
     *     mobileDrawerPanelContentClasses: string,
     *     mobileDrawerListClasses: string,
     *     mobileDrawerBackButtonClasses: string,
     *     mobileDrawerBackLabelClasses: string,
     *     mobileDrawerLinkClasses: string,
     *     mobileDrawerTextLinkClasses: string
     * }
     */
    protected function navigationViewData(Request $request): array
    {
        $currentPage = $request->route('page');

        return [
            'currentRuleset' => $request->route('ruleset'),
            'currentPage' => $currentPage,
            'isRulesetRoute' => $request->routeIs('ruleset.show', 'ruleset.section.show', 'table.index', 'fixture.index', 'player.index'),
            'isKnockoutRoute' => $request->routeIs('knockout.*')
                || ($request->routeIs('page.show') && $currentPage === 'knockout-dates'),
            'mobileDrawerPanelClasses' => 'absolute inset-0 overflow-y-auto bg-white px-4 py-4 dark:bg-zinc-900',
            'mobileDrawerPanelContentClasses' => 'space-y-5',
            'mobileDrawerListClasses' => 'space-y-1',
            'mobileDrawerBackButtonClasses' => 'block w-full border-b border-gray-200 pb-3 text-left dark:border-gray-800',
            'mobileDrawerBackLabelClasses' => 'flex items-center gap-3 py-3 text-base font-semibold leading-7 text-gray-900 transition hover:text-gray-700 dark:text-gray-100 dark:hover:text-gray-200',
            'mobileDrawerLinkClasses' => 'flex w-full items-center justify-between rounded-lg px-0 py-3 text-left text-base font-semibold leading-7 text-gray-900 transition hover:text-gray-700 dark:text-gray-100 dark:hover:text-gray-200',
            'mobileDrawerTextLinkClasses' => 'block rounded-lg px-0 py-3 text-base font-semibold leading-7 text-gray-900 transition hover:text-gray-700 dark:text-gray-100 dark:hover:text-gray-200',
        ];
    }

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

    protected function pastSeasons(): Collection|array
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

    protected function activeKnockouts(): Collection
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

    protected function navigationRulesets(): Collection
    {
        return Cache::remember(self::NAVIGATION_RULESETS_CACHE_KEY, now()->addMinutes(10), function () {
            return $this->rulesets()
                ->map(function (Ruleset $ruleset): array {
                    return [
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

    protected function navigableActiveKnockouts(): Collection
    {
        return Cache::remember(self::NAVIGABLE_ACTIVE_KNOCKOUTS_CACHE_KEY, now()->addMinutes(10), function () {
            return $this->activeKnockouts()
                ->filter(fn ($knockout) => filled($knockout?->slug))
                ->values();
        });
    }

    protected function historySeasonGroups(): Collection
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
            return $this->pastSeasons()
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

                    $knockouts = $season->knockouts
                        ->filter(fn ($knockout) => filled($knockout?->slug))
                        ->values();

                    return [
                        'season' => $season,
                        'rulesets' => $rulesets,
                        'knockouts' => $knockouts,
                    ];
                });
        });
    }
}
