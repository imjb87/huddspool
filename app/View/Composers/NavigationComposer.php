<?php

namespace App\View\Composers;

use App\Models\Knockout;
use App\Models\Ruleset;
use App\Models\Season;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class NavigationComposer
{
    private const RULESETS_CACHE_KEY = 'nav:rulesets:v4';

    private const ACTIVE_KNOCKOUTS_CACHE_KEY = 'nav:active-knockouts:v2';

    public function compose(View $view): void
    {
        $view->with([
            'rulesets' => $this->rulesets(),
            'past_seasons' => $this->pastSeasons(),
            'active_knockouts' => $this->activeKnockouts(),
        ]);
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
                ->with(['sections.ruleset:id,name,slug'])
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
}
