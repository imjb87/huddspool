<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gpt\UpdateFixtureVenueRequest;
use App\Models\Fixture;
use App\Models\Venue;
use App\Services\Gpt\UpdateFixtureVenue;
use Illuminate\Http\JsonResponse;

class FixtureVenueController extends Controller
{
    public function __invoke(UpdateFixtureVenueRequest $request, Fixture $fixture, UpdateFixtureVenue $updateFixtureVenue): JsonResponse
    {
        $venue = Venue::query()->findOrFail($request->integer('venue_id'));
        $audit = $updateFixtureVenue->handle(
            administrator: $request->user(),
            fixture: $fixture,
            venue: $venue,
            expectedVenueId: $request->input('expected_current_venue_id') === null ? null : $request->integer('expected_current_venue_id'),
            expectedUpdatedAt: $request->string('expected_updated_at')->toString(),
            reason: $request->string('reason')->toString(),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        $fixture->refresh()->loadMissing('homeTeam', 'awayTeam', 'venue');

        return response()->json([
            'message' => 'The fixture venue was updated.',
            'fixture' => [
                'id' => $fixture->id,
                'fixture_date' => $fixture->fixture_date?->toDateString(),
                'home_team' => ['id' => $fixture->homeTeam?->id, 'name' => $fixture->homeTeam?->name],
                'away_team' => ['id' => $fixture->awayTeam?->id, 'name' => $fixture->awayTeam?->name],
                'venue' => ['id' => $fixture->venue?->id, 'name' => $fixture->venue?->name],
                'updated_at' => $fixture->attributesToArray()['updated_at'] ?? null,
            ],
            'change' => ['before' => $audit->before, 'after' => $audit->after],
            'audit_id' => $audit->id,
        ]);
    }
}
