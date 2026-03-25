<?php

namespace App\Support;

use App\Models\Fixture;

class FixtureShowPageData
{
    public function build(Fixture $fixture): object
    {
        $standings = $fixture->section->standings()
            ->values()
            ->map(fn ($team, $index) => StandingSummaryRow::fromStanding($team, $index + 1, false));

        return (object) [
            'standings' => $standings
                ->filter(function ($standing) use ($fixture) {
                    return (int) $standing->id === (int) $fixture->home_team_id
                        || (int) $standing->id === (int) $fixture->away_team_id;
                })
                ->values(),
        ];
    }
}
