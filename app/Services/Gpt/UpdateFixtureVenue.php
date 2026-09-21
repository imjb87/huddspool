<?php

namespace App\Services\Gpt;

use App\Models\Fixture;
use App\Models\GptActionAudit;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateFixtureVenue
{
    public function handle(
        User $administrator,
        Fixture $fixture,
        Venue $venue,
        ?int $expectedVenueId,
        Carbon $expectedUpdatedAt,
        string $reason,
        ?string $ipAddress,
        ?string $userAgent,
    ): GptActionAudit {
        return DB::transaction(function () use ($administrator, $fixture, $venue, $expectedVenueId, $expectedUpdatedAt, $reason, $ipAddress, $userAgent): GptActionAudit {
            $lockedFixture = Fixture::query()->with('venue')->lockForUpdate()->findOrFail($fixture->id);

            if ($lockedFixture->venue_id !== $expectedVenueId) {
                throw ValidationException::withMessages(['expected_current_venue_id' => 'The fixture venue changed after it was inspected. Inspect the fixture again before retrying.']);
            }

            if (! $lockedFixture->updated_at?->equalTo($expectedUpdatedAt)) {
                throw ValidationException::withMessages(['expected_updated_at' => 'The fixture changed after it was inspected. Inspect the fixture again before retrying.']);
            }

            if ($lockedFixture->venue_id === $venue->id) {
                throw ValidationException::withMessages(['venue_id' => 'The fixture already uses this venue.']);
            }

            $before = [
                'venue_id' => $lockedFixture->venue_id,
                'venue_name' => $lockedFixture->venue?->name,
                'updated_at' => $lockedFixture->updated_at?->toAtomString(),
                'reason' => $reason,
            ];

            $lockedFixture->update(['venue_id' => $venue->id]);
            $lockedFixture->load('venue');

            return GptActionAudit::query()->create([
                'administrator_id' => $administrator->id,
                'action' => 'update_fixture_venue',
                'subject_type' => Fixture::class,
                'subject_id' => $lockedFixture->id,
                'before' => $before,
                'after' => [
                    'venue_id' => $lockedFixture->venue_id,
                    'venue_name' => $lockedFixture->venue?->name,
                    'updated_at' => $lockedFixture->updated_at?->toAtomString(),
                    'reason' => $reason,
                ],
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
        });
    }
}
