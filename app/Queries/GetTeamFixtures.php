<?php

namespace App\Queries;

use App\Data\TeamFixtureData;
use App\Models\Team;
use App\Models\Section;

class GetTeamFixtures
{
    public function __construct(
        protected Team $team,
        protected Section $section,
    ) {}

    public function __invoke()
    {
        return $this->team->fixtures()
            ->where('section_id', $this->section->id)
            ->with([
                'homeTeam' => fn ($query) => $query->withTrashed(),
                'awayTeam' => fn ($query) => $query->withTrashed(),
                'result',
            ])
            ->orderBy('week')
            ->get()
            ->map(fn ($fixture) => TeamFixtureData::fromFixture($fixture));
    }
}
