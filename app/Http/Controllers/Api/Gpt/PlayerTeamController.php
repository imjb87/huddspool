<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gpt\MovePlayerRequest;
use App\Models\Team;
use App\Models\User;
use App\Services\Gpt\MovePlayer;
use Illuminate\Http\JsonResponse;

class PlayerTeamController extends Controller
{
    public function __invoke(MovePlayerRequest $request, User $player, MovePlayer $movePlayer): JsonResponse
    {
        $destinationTeam = Team::query()->findOrFail($request->integer('destination_team_id'));
        $audit = $movePlayer->handle(
            administrator: $request->user(),
            player: $player,
            destinationTeam: $destinationTeam,
            expectedCurrentTeamId: $request->input('expected_current_team_id') === null
                ? null
                : $request->integer('expected_current_team_id'),
            makeDestinationCaptain: $request->boolean('make_destination_captain'),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return response()->json([
            'message' => sprintf('%s was moved to %s.', $player->name, $destinationTeam->name),
            'change' => [
                'before' => $audit->before,
                'after' => $audit->after,
            ],
            'audit_id' => $audit->id,
        ]);
    }
}
