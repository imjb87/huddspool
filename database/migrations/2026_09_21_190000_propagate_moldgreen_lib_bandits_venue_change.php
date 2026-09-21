<?php

use App\Models\Team;
use App\Models\Venue;
use App\Services\PropagateTeamVenueToFixtures;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $team = Team::query()->find(73);

            if (! $team || $team->venue_id !== 36) {
                return;
            }

            if (! Venue::query()->whereKey(44)->exists() || ! Venue::query()->whereKey(36)->exists()) {
                return;
            }

            app(PropagateTeamVenueToFixtures::class)->handle($team, 44, 36);
        });
    }

    public function down(): void
    {
        //
    }
};
