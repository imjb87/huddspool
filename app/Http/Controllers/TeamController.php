<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Queries\GetTeamFixtures;
use App\Queries\GetTeamKnockoutMatches;
use App\Queries\GetTeamPlayers;
use App\Queries\GetTeamSeasonHistory;
use App\Support\FixtureSummaryRow;
use App\Support\KnockoutMatchSummaryRow;
use App\Support\OrdinalFormatter;
use App\Support\TeamHistoryRow;
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
        $fixtureRows = $fixtures->map(fn ($fixture) => FixtureSummaryRow::fromTeamFixtureData($fixture, $team->id));

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

        $history = (new GetTeamSeasonHistory($team))()
            ->filter(fn (array $entry): bool => $entry['season_id'] !== $section->season_id)
            ->values();
        $historyRows = $history->map(fn (array $entry) => TeamHistoryRow::fromEntry($entry));

        $teamKnockoutMatches = new GetTeamKnockoutMatches($team)();
        $allowKnockoutSubmission = auth()->user()?->isAdmin() ?? false;
        $teamKnockoutRows = $teamKnockoutMatches->map(fn ($match) => KnockoutMatchSummaryRow::forTeam($match, $team, $allowKnockoutSubmission));

        return view('team.show', compact('team', 'fixtures', 'fixtureRows', 'players', 'section', 'currentStanding', 'history', 'historyRows', 'teamKnockoutMatches', 'teamKnockoutRows'));
    }
}
