<?php

namespace App\Http\Controllers;

use App\Models\Team;

class TeamController extends Controller
{
    public function show(Team $team)
    {
        if ($team->id === 1) {
            abort(404);
        }

        // Eager load players with their counts and the team's venue.
        $team->load([
            'players' => function ($query) {
                $query->withCount(['frames', 'framesWon', 'framesLost']);
            },
            'venue'
        ]);

        // Retrieve fixtures for this team with related result, homeTeam, and awayTeam eager loaded.
        $fixtures = \App\Models\Fixture::with(['result', 'homeTeam', 'awayTeam'])
            ->where(function ($query) use ($team) {
                $query->where('home_team_id', $team->id)
                    ->orWhere('away_team_id', $team->id);
            })
            ->whereHas('season', function ($query) {
                $query->where('is_open', true);
            })
            ->get();

        return view('team.show', compact('team', 'fixtures'));
    }
}
