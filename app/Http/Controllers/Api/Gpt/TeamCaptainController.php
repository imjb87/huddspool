<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gpt\UpdateTeamCaptainRequest;
use App\Models\Team;
use App\Models\User;
use App\Services\Gpt\UpdateTeamCaptain;
use Illuminate\Http\JsonResponse;

class TeamCaptainController extends Controller
{
    public function __invoke(UpdateTeamCaptainRequest $request, Team $team, UpdateTeamCaptain $updateTeamCaptain): JsonResponse
    {
        $captain = $request->input('captain_id') === null ? null : User::query()->findOrFail($request->integer('captain_id'));
        $audit = $updateTeamCaptain->handle(
            administrator: $request->user(),
            team: $team,
            captain: $captain,
            expectedCaptainId: $request->input('expected_current_captain_id') === null ? null : $request->integer('expected_current_captain_id'),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return response()->json([
            'message' => $captain === null ? sprintf('%s now has no captain.', $team->name) : sprintf('%s is now captain of %s.', $captain->name, $team->name),
            'change' => ['before' => $audit->before, 'after' => $audit->after],
            'audit_id' => $audit->id,
        ]);
    }
}
