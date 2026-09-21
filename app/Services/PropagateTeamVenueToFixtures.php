<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Support\Carbon;

class PropagateTeamVenueToFixtures
{
    public function handle(Team $team, ?int $previousVenueId, int $newVenueId): int
    {
        return Fixture::query()
            ->where('home_team_id', $team->id)
            ->where('fixture_date', '>=', Carbon::now()->startOfDay())
            ->whereDoesntHave('result')
            ->when(
                $previousVenueId === null,
                fn ($query) => $query->whereNull('venue_id'),
                fn ($query) => $query->where('venue_id', $previousVenueId),
            )
            ->lockForUpdate()
            ->update(['venue_id' => $newVenueId]);
    }
}
