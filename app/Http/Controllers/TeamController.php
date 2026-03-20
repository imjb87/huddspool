<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Queries\GetTeamFixtures;
use App\Queries\GetTeamKnockoutMatches;
use App\Queries\GetTeamPlayers;
use App\Queries\GetTeamSeasonHistory;
use Illuminate\Contracts\View\View;

class TeamController extends Controller
{
    public function show(Team $team): View
    {
        if ($team->isBye()) {
            abort(404);
        }

        $section = $team->openSection();

        if (! $section) {
            abort(404);
        }

        // Retrieve players for this team with frames played, frames won, and frames lost.
        $players = new GetTeamPlayers($team, $section)();

        // Retrieve fixtures for this team with related result, homeTeam, and awayTeam eager loaded.
        $fixtures = new GetTeamFixtures($team, $section)();

        $history = (new GetTeamSeasonHistory($team))();

        $teamKnockoutMatches = new GetTeamKnockoutMatches($team)();

        return view('team.show', compact('team', 'fixtures', 'players', 'section', 'history', 'teamKnockoutMatches'));
    }
}
