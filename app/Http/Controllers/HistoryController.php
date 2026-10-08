<?php

namespace App\Http\Controllers;

use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function section(Season $season, Ruleset $ruleset, string $section): View
    {
        abort_if(! $season->hasConcluded(), 404);

        $historySection = Section::withTrashed()
            ->where('season_id', $season->id)
            ->where('ruleset_id', $ruleset->id)
            ->where('slug', $section)
            ->firstOrFail();

        return view('history.section', [
            'season' => $season,
            'ruleset' => $ruleset,
            'section' => $historySection,
        ]);
    }
}
