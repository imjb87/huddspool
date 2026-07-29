<?php

namespace App\Services\Gpt;

use App\Models\Fixture;
use App\Models\GptActionAudit;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateFixtureDate
{
    public function handle(User $administrator, Fixture $fixture, Carbon $fixtureDate, Carbon $expectedFixtureDate, ?string $ipAddress, ?string $userAgent): GptActionAudit
    {
        return DB::transaction(function () use ($administrator, $fixture, $fixtureDate, $expectedFixtureDate, $ipAddress, $userAgent): GptActionAudit {
            $lockedFixture = Fixture::query()->lockForUpdate()->findOrFail($fixture->id);

            if ($lockedFixture->fixture_date?->toDateString() !== $expectedFixtureDate->toDateString()) {
                throw ValidationException::withMessages(['expected_current_fixture_date' => 'The fixture date changed after it was inspected. Inspect the fixture again before retrying.']);
            }

            if ($lockedFixture->fixture_date->toDateString() === $fixtureDate->toDateString()) {
                throw ValidationException::withMessages(['fixture_date' => 'The fixture already has this date.']);
            }

            $before = ['fixture_date' => $lockedFixture->fixture_date->toDateString()];
            $lockedFixture->update(['fixture_date' => $fixtureDate->toDateString()]);

            return GptActionAudit::query()->create([
                'administrator_id' => $administrator->id,
                'action' => 'update_fixture_date',
                'subject_type' => Fixture::class,
                'subject_id' => $lockedFixture->id,
                'before' => $before,
                'after' => ['fixture_date' => $lockedFixture->fixture_date->toDateString()],
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
        });
    }
}
