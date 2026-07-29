<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\Result;
use App\Queries\GetOpenSeasonStats;
use daacreators\CreatorsTicketing\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(GetOpenSeasonStats $getOpenSeasonStats): JsonResponse
    {
        $stats = $getOpenSeasonStats();

        $outstandingFixtures = Fixture::query()
            ->with(['result', 'section', 'homeTeam', 'awayTeam'])
            ->whereDoesntHave('result', fn (Builder $query) => $query->where('is_confirmed', true))
            ->whereHas('season', fn (Builder $query) => $query->where('is_open', true))
            ->whereHas('homeTeam', fn (Builder $query) => $query->notBye())
            ->whereHas('awayTeam', fn (Builder $query) => $query->notBye())
            ->where('fixture_date', '<', now())
            ->orderBy('fixture_date')
            ->limit(10)
            ->get()
            ->map(fn (Fixture $fixture): array => [
                'id' => $fixture->id,
                'fixture_date' => $fixture->fixture_date,
                'home_team' => ['id' => $fixture->homeTeam->id, 'name' => $fixture->homeTeam->name],
                'away_team' => ['id' => $fixture->awayTeam->id, 'name' => $fixture->awayTeam->name],
                'section' => $fixture->section ? ['id' => $fixture->section->id, 'name' => $fixture->section->name] : null,
                'submitted_score' => $fixture->result ? $fixture->result->home_score.' - '.$fixture->result->away_score : null,
            ]);

        $latestResults = Result::query()
            ->where('is_confirmed', true)
            ->whereHas('fixture.season', fn (Builder $query) => $query->where('is_open', true))
            ->orderByRaw('COALESCE(submitted_at, created_at) desc')
            ->limit(5)
            ->get()
            ->map(fn (Result $result): array => [
                'id' => $result->id,
                'home_team' => $result->home_team_name,
                'home_score' => $result->home_score,
                'away_team' => $result->away_team_name,
                'away_score' => $result->away_score,
                'submitted_at' => $result->submitted_at,
            ]);

        return response()->json([
            'open_season' => [
                'total_frames' => $stats->totalFrames,
                'total_results' => $stats->totalResults,
                'total_players' => $stats->totalPlayers,
            ],
            'open_support_tickets' => Ticket::query()
                ->whereHas('status', fn (Builder $query) => $query->where('is_closing_status', false))
                ->count(),
            'outstanding_fixtures' => $outstandingFixtures,
            'latest_results' => $latestResults,
        ]);
    }
}
