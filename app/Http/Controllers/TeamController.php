<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Queries\GetTeamKnockoutMatches;
use App\Support\KnockoutMatchSummaryRow;
use App\Support\OrdinalFormatter;
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

        $standings = $section->standings()->values();
        $standingIndex = $standings->search(fn ($standing) => (int) $standing->id === (int) $team->id);
        $currentStanding = $standingIndex === false
            ? null
            : (object) [
                'position' => $standingIndex + 1,
                'label' => OrdinalFormatter::format($standingIndex + 1).' of '.$standings->count(),
                'points' => (int) ($standings->get($standingIndex)->points ?? 0),
                'played' => (int) ($standings->get($standingIndex)->played ?? 0),
            ];

        $teamKnockoutMatches = new GetTeamKnockoutMatches($team)();
        $allowKnockoutSubmission = auth()->check();
        $teamKnockoutRows = $teamKnockoutMatches->map(fn ($match) => KnockoutMatchSummaryRow::forTeam($match, $team, $allowKnockoutSubmission));

        return view('team.show', compact('team', 'section', 'currentStanding', 'teamKnockoutMatches', 'teamKnockoutRows'));
    }
}
