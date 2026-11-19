<?php

namespace App\View\Composers;

use App\Domain\Navigation\NavigationDataProvider;
use App\Domain\Navigation\NavigationViewClasses;
use App\Domain\Navigation\NavigationViewState;
use App\Models\Ruleset;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use STS\FilamentImpersonate\Facades\Impersonation;

class NavigationComposer
{
    private ?NavigationDataProvider $provider = null;

    public function compose(View $view): void
    {
        $request = request();
        $navigationRulesets = $this->provider()->navigationRulesets();

        $view->with([
            'rulesets' => $navigationRulesets->pluck('ruleset'),
            'past_seasons' => $this->provider()->pastSeasons(),
            'navigationRulesets' => $this->desktopNavigationRulesets($request, $navigationRulesets),
            'historySeasonGroups' => $this->provider()->historySeasonGroups(),
            'navigableKnockouts' => $this->provider()->activeKnockouts(),
            'is_impersonating' => Impersonation::isImpersonating(),
        ] + NavigationViewClasses::defaults() + NavigationViewState::fromRequest($request));
    }

    protected function desktopNavigationRulesets(Request $request, Collection $navigationRulesets): Collection
    {
        $currentRuleset = $request->route('ruleset');
        $isRulesetRoute = $request->routeIs('ruleset.show', 'ruleset.rules', 'ruleset.section.show', 'table.index', 'fixture.index', 'player.index');

        return $navigationRulesets
            ->map(function (array $navigationRuleset) use ($currentRuleset, $isRulesetRoute): array {
                $ruleset = $navigationRuleset['ruleset'];

                return $navigationRuleset + [
                    'is_active' => $isRulesetRoute
                        && $currentRuleset instanceof Ruleset
                        && $currentRuleset->is($ruleset),
                ];
            });
    }

    private function provider(): NavigationDataProvider
    {
        return $this->provider ??= new NavigationDataProvider;
    }
}
