<?php

namespace App\Http\Controllers;

use App\Models\Ruleset;
use App\Models\Fixture;
use App\Queries\GetTeamPlayers;

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

        $home_team_players = new GetTeamPlayers($fixture->homeTeam, $fixture->section)();
        $away_team_players = new GetTeamPlayers($fixture->awayTeam, $fixture->section)();

        return view('fixture.show', compact('fixture', 'home_team_players', 'away_team_players'));
    }
}
