<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('query'));

        $teams = Team::query()
            ->notBye()
            ->inOpenSeason()
            ->with(['venue', 'captain', 'openSections.ruleset'])
            ->when($query !== '', fn (Builder $builder) => $builder->where('name', 'like', '%'.$query.'%'))
            ->orderBy('name')
            ->limit(30)
            ->get()
            ->map(fn (Team $team): array => $this->teamData($team));

        return response()->json([
            'query' => $query,
            'count' => $teams->count(),
            'teams' => $teams,
        ]);
    }

    public function roster(Team $team): JsonResponse
    {
        $team->load(['venue', 'captain', 'openSections.ruleset', 'players' => fn (Builder $query) => $query->orderBy('name')]);

        return response()->json([
            'team' => $this->teamData($team),
            'players' => $team->players->map(fn (User $player): array => [
                'id' => $player->id,
                'name' => $player->name,
                'is_captain' => $team->captain_id === $player->id,
            ]),
        ]);
    }

    private function teamData(Team $team): array
    {
        $section = $team->openSection();

        return [
            'id' => $team->id,
            'name' => $team->name,
            'venue' => $team->venue ? [
                'id' => $team->venue->id,
                'name' => $team->venue->name,
            ] : null,
            'captain' => $team->captain ? [
                'id' => $team->captain->id,
                'name' => $team->captain->name,
            ] : null,
            'section' => $section ? [
                'id' => $section->id,
                'name' => $section->name,
                'ruleset' => $section->ruleset?->name,
            ] : null,
        ];
    }
}
