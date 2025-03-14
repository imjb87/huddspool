<?php

namespace App\Http\Controllers;

use App\Models\Ruleset;
use App\Models\Fixture;

class FixtureController extends Controller
{
    public function index(Ruleset $ruleset)
    {
        $sections = $ruleset->sections()
            ->whereHas('season', function ($query) {
                $query->whereIsOpen(true);
            })->get();

        return view('fixture.index', compact('ruleset', 'sections'));
    }

    public function show(Fixture $fixture)
    {
        if ($fixture->home_team_id == 1 || $fixture->away_team_id == 1) {
            abort(404);
        }

        // Eager load players with counts for frames, framesWon, and framesLost.
        $fixture->load([
            'homeTeam.players' => function ($query) {
                $query->withCount(['frames', 'framesWon', 'framesLost']);
            },
            'awayTeam.players' => function ($query) {
                $query->withCount(['frames', 'framesWon', 'framesLost']);
            },
        ]);        

        return view('fixture.show', compact('fixture'));
    }
}
