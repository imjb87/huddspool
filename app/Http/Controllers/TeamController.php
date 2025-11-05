<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Queries\GetTeamFixtures;
use App\Queries\GetTeamSeasonHistory;
use App\Queries\GetTeamPlayers;

class TeamController extends Controller
{
    public function show(Team $team)
    {
        if ($team->id === 1) {
            abort(404);
        }

        $section = $team->section();

        // Retrieve players for this team with frames played, frames won, and frames lost.
        $players = new GetTeamPlayers($team, $section)();

        // Retrieve fixtures for this team with related result, homeTeam, and awayTeam eager loaded.
        $fixtures = new GetTeamFixtures($team, $section)();

        $history = (new GetTeamSeasonHistory($team))();

        return view('team.show', compact('team', 'fixtures', 'players', 'section', 'history'));
    }
}
