<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gpt\UpdateFixtureDateRequest;
use App\Models\Fixture;
use App\Services\Gpt\UpdateFixtureDate;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class FixtureDateController extends Controller
{
    public function __invoke(UpdateFixtureDateRequest $request, Fixture $fixture, UpdateFixtureDate $updateFixtureDate): JsonResponse
    {
        $audit = $updateFixtureDate->handle(
            administrator: $request->user(),
            fixture: $fixture,
            fixtureDate: Carbon::parse($request->string('fixture_date')),
            expectedFixtureDate: Carbon::parse($request->string('expected_current_fixture_date')),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return response()->json([
            'message' => 'The fixture date was updated.',
            'change' => ['before' => $audit->before, 'after' => $audit->after],
            'audit_id' => $audit->id,
        ]);
    }
}
