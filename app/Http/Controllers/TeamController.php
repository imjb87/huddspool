<?php

namespace App\Http\Controllers;

use App\Models\Team;

class TeamController extends Controller
{
    public function show(Team $team)
    {
        $team->load([
            'players' => function ($query) {
                $query->withCount(['frames', 'framesWon', 'framesLost']);
            }
        ]);

        return view('team.show', compact('team'));
    }
}