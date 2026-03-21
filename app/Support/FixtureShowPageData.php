<?php

namespace App\Support;

use App\Models\Fixture;

class FixtureShowPageData
{
    public function build(Fixture $fixture): object
    {
        return (object) [
            'standings' => $fixture->section->standings()
                ->filter(function ($standing) use ($fixture) {
                    return (int) $standing->id === (int) $fixture->home_team_id
                        || (int) $standing->id === (int) $fixture->away_team_id;
                })
                ->values(),
        ];
    }
}
