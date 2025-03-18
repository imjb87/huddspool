<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Queries\GetTeamPlayers;
use App\Queries\GetTeamFixtures;

class TeamController extends Controller
{
    public function show(Team $team)
    {
        if ($team->id === 1) {
            abort(404);
        }

        // Retrieve players for this team with frames played, frames won, and frames lost.
        $players = new GetTeamPlayers($team, $team->section())();

        // Retrieve fixtures for this team with related result, homeTeam, and awayTeam eager loaded.
        $fixtures = new GetTeamFixtures($team, $team->section())();

        return view('team.show', compact('team', 'fixtures', 'players'));
    }
}
