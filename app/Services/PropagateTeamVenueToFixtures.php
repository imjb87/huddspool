<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PropagateTeamVenueToFixtures
{
    /**
     * @return Collection<int, Fixture>
     */
    public function handle(Team $team, ?int $previousVenueId, int $newVenueId): Collection
    {
        return Fixture::query()
            ->where('home_team_id', $team->id)
            ->whereDate('fixture_date', '>=', Carbon::today()->toDateString())
            ->whereDoesntHave('result')
            ->when(
                $previousVenueId === null,
                fn ($query) => $query->whereNull('venue_id'),
                fn ($query) => $query->where('venue_id', $previousVenueId),
            )
            ->lockForUpdate()
            ->get()
            ->each(function (Fixture $fixture) use ($newVenueId): void {
                $fixture->update(['venue_id' => $newVenueId]);
            });
    }
}
