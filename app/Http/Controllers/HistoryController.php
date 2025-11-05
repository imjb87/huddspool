<?php

namespace App\Http\Controllers;

use App\Models\Season;
use App\Models\Ruleset;
use App\Models\Section;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(): View
    {
        $seasons = Cache::remember('history:index', now()->addMinutes(10), function () {
            return Season::query()
                ->where('is_open', false)
                ->with(['sections.ruleset:id,name,slug'])
                ->orderByDesc('id')
                ->get()
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
