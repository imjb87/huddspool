<?php

namespace App\Http\Controllers;

use App\Models\Season;
use App\Models\Ruleset;
use App\Models\Section;
use App\Queries\GetSectionAverages;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(): View
    {
        $seasons = Cache::remember('history:index', now()->addMinutes(10), function () {
            return Season::query()
                ->with(['sections.ruleset:id,name,slug'])
                ->orderByDesc('id')
                ->get()
                ->filter(fn (Season $season) => $season->hasConcluded())
                ->values()
                ->map(function (Season $season) {
                    $rulesets = $season->sections
                        ->map(fn ($section) => $section->ruleset)
                        ->filter()
                        ->unique('id')
                        ->values();

                    return [
                        'season' => $season,
                        'rulesets' => $rulesets,
                    ];
                });
        });

        return view('history.index', [
            'seasonGroups' => $seasons,
        ]);
    }

    public function season(Season $season): View
    {
        abort_if(! $season->hasConcluded(), 404);

        $overview = Cache::remember(
            sprintf('history:season:%d', $season->id),
            now()->addMinutes(10),
            function () use ($season) {
                $rulesetOrder = [
                    'international-rules' => 0,
                    'blackball-rules' => 1,
                    'epa-rules' => 2,
                ];

                return $season->sections()
                    ->with('ruleset:id,name,slug')
                    ->get()
                    ->sortBy(function (Section $section) use ($rulesetOrder) {
                        $priority = $rulesetOrder[$section->ruleset->slug ?? ''] ?? PHP_INT_MAX;

                        return sprintf('%03d-%s', $priority, $section->name);
                    })
                    ->map(function (Section $section) {
                        $standings = $section->standings();
                        $winner = $standings->first();
                        $runnerUp = $standings->slice(1, 1)->first();
                        $averageWinner = (new GetSectionAverages($section, 1, 1))()->first();

                        return [
                            'section' => $section,
                            'winner' => $winner,
                            'runner_up' => $runnerUp,
                            'average_winner' => $averageWinner,
                        ];
                    });
            }
        );

        return view('history.season', [
            'season' => $season,
            'overview' => $overview,
        ]);
    }

    public function show(Season $season, Ruleset $ruleset): View
    {
        $sections = Cache::remember(
            sprintf('history:sections:%d:%d', $season->id, $ruleset->id),
            now()->addMinutes(10),
            fn () => Section::query()
                ->where('season_id', $season->id)
                ->where('ruleset_id', $ruleset->id)
                ->orderBy('name')
                ->get()
        );

        return view('history.show', [
            'season' => $season,
            'ruleset' => $ruleset,
            'sections' => $sections,
        ]);
    }
}
