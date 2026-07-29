<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gpt\UpdateTeamVenueRequest;
use App\Models\Team;
use App\Models\Venue;
use App\Services\Gpt\UpdateTeamVenue;
use Illuminate\Http\JsonResponse;

class TeamVenueController extends Controller
{
    public function __invoke(UpdateTeamVenueRequest $request, Team $team, UpdateTeamVenue $updateTeamVenue): JsonResponse
    {
        $venue = Venue::query()->findOrFail($request->integer('venue_id'));
        $audit = $updateTeamVenue->handle(
            administrator: $request->user(),
            team: $team,
            venue: $venue,
            expectedVenueId: $request->input('expected_current_venue_id') === null ? null : $request->integer('expected_current_venue_id'),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return response()->json([
            'message' => sprintf('%s now uses %s.', $team->name, $venue->name),
            'change' => ['before' => $audit->before, 'after' => $audit->after],
            'audit_id' => $audit->id,
        ]);
    }
}
