<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gpt\ShowPlayerProfileRequest;
use App\Models\User;
use App\Queries\GetPlayerAverages;
use App\Queries\GetPlayerFrames;
use App\Queries\GetPlayerKnockoutMatches;
use App\Queries\GetPlayerSeasonHistory;
use App\Support\FrameSummaryRow;
use App\Support\KnockoutMatchSummaryRow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function show(ShowPlayerProfileRequest $request, User $player): JsonResponse
    {
        $player->loadMissing(['team.venue', 'team.openSections.ruleset']);
        $section = $player->team?->openSection();
        $averages = (new GetPlayerAverages($player, $section))();
        $frames = (new GetPlayerFrames($player, $section, $request->integer('frames_page', 1)))();
        $knockoutMatches = (new GetPlayerKnockoutMatches($player))();

        return response()->json([
            'player' => $this->playerData($player),
            'venue' => $player->team?->venue ? ['id' => $player->team->venue->id, 'name' => $player->team->venue->name] : null,
            'current_section_averages' => [
                'frames_played' => $averages->frames_played,
                'frames_won' => $averages->frames_won,
                'frames_won_percentage' => $averages->frames_won_percentage,
                'frames_lost' => $averages->frames_lost,
                'frames_lost_percentage' => $averages->frames_lost_percentage,
            ],
            'recent_frames' => collect($frames->items())
                ->map(fn (object $frame): object => FrameSummaryRow::fromFrame($frame, $player->id))
                ->values(),
            'frames_pagination' => ['current_page' => $frames->currentPage(), 'last_page' => $frames->lastPage(), 'total' => $frames->total()],
            'season_history' => (new GetPlayerSeasonHistory($player))(),
            'knockout_matches' => $knockoutMatches->map(function ($match) use ($player): array {
                $row = KnockoutMatchSummaryRow::forPlayer($match, $player, false);

                return [
                    'id' => $row->id,
                    'competition_and_round' => $row->meta_label,
                    'home' => $row->home_label,
                    'away' => $row->away_label,
                    'home_score' => $row->home_score,
                    'away_score' => $row->away_score,
                    'has_result' => $row->has_result,
                    'venue' => $row->venue_label,
                    'date' => $row->date_label,
                ];
            })->values(),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('query'));

        if (mb_strlen($query) < 2) {
            return response()->json([
                'message' => 'Enter at least two characters to search for a player.',
            ], 422);
        }

        $players = User::query()
            ->with(['team.openSections.ruleset'])
            ->where('name', 'like', '%'.$query.'%')
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(fn (User $player): array => $this->playerData($player));

        return response()->json([
            'query' => $query,
            'count' => $players->count(),
            'players' => $players,
        ]);
    }

    private function playerData(User $player): array
    {
        $section = $player->team?->openSection();

        return [
            'id' => $player->id,
            'name' => $player->name,
            'team' => $player->team ? [
                'id' => $player->team->id,
                'name' => $player->team->name,
            ] : null,
            'section' => $section ? [
                'id' => $section->id,
                'name' => $section->name,
                'ruleset' => $section->ruleset?->name,
            ] : null,
            'is_team_captain' => $player->team?->captain_id === $player->id,
        ];
    }
}
