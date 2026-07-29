<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
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
