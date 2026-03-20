<?php

namespace App\Http\Controllers;

use App\KnockoutType;
use App\Models\KnockoutMatch;
use App\Models\Team;
use App\Queries\GetTeamFixtures;
use App\Queries\GetTeamPlayers;
use App\Queries\GetTeamSeasonHistory;
use Illuminate\Contracts\View\View;

class TeamController extends Controller
{
    public function show(Team $team): View
    {
        if ($team->id === 1) {
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

        $teamKnockoutMatches = KnockoutMatch::query()
            ->with([
                'round.knockout',
                'homeParticipant',
                'awayParticipant',
                'winner',
            ])
            ->whereHas('round', fn ($query) => $query->where('is_visible', true))
            ->whereHas('round.knockout', fn ($query) => $query->where('type', KnockoutType::Team))
            ->where(function ($query) use ($team) {
                $query->whereHas('homeParticipant', fn ($participantQuery) => $participantQuery->where('team_id', $team->id))
                    ->orWhereHas('awayParticipant', fn ($participantQuery) => $participantQuery->where('team_id', $team->id));
            })
            ->orderByDesc('starts_at')
            ->orderByDesc('id')
            ->get();

        return view('team.show', compact('team', 'fixtures', 'players', 'section', 'history', 'teamKnockoutMatches'));
    }
}
