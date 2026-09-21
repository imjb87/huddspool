<?php

use App\Models\Team;
use App\Models\Venue;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
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

            DB::table('fixtures')
                ->where('home_team_id', $team->id)
                ->where('fixture_date', '>=', Carbon::now()->startOfDay())
                ->where('venue_id', 44)
                ->whereNotExists(function ($query): void {
                    $query
                        ->select(DB::raw(1))
                        ->from('results')
                        ->whereColumn('results.fixture_id', 'fixtures.id');
                })
                ->update(['venue_id' => 36]);
        });
    }

    public function down(): void
    {
        //
    }
};
