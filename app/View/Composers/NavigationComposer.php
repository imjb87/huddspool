<?php

namespace App\View\Composers;

use App\Models\Ruleset;
use App\Support\NavigationDataProvider;
use App\Support\NavigationViewClasses;
use App\Support\NavigationViewState;
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

        $view->with([
            'rulesets' => $this->provider()->navigationRulesets()->pluck('ruleset'),
            'past_seasons' => $this->provider()->pastSeasons(),
            'active_knockouts' => $this->provider()->activeKnockouts(),
            'navigationRulesets' => $this->desktopNavigationRulesets($request),
            'historySeasonGroups' => $this->provider()->historySeasonGroups(),
            'navigableKnockouts' => $this->provider()->navigableActiveKnockouts(),
            'is_impersonating' => Impersonation::isImpersonating(),
        ] + NavigationViewClasses::defaults() + NavigationViewState::fromRequest($request));
    }

    protected function desktopNavigationRulesets(Request $request): Collection
    {
        $currentRuleset = $request->route('ruleset');
        $isRulesetRoute = $request->routeIs('ruleset.show', 'ruleset.rules', 'ruleset.section.show', 'table.index', 'fixture.index', 'player.index');

        return $this->provider()
            ->navigationRulesets()
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
