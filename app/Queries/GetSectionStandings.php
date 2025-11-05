<?php

namespace App\Queries;

use App\Data\SectionStandingData;
use App\Models\Section;
use Illuminate\Support\Collection;

class GetSectionStandings
{
    public function __construct(
        protected Section $section,
    ) {
    }

    /**
     * @return Collection<int, SectionStandingData>
     */
    public function __invoke(): Collection
    {
        return $this->section
            ->standings()
            ->map(function ($team) {
                return new SectionStandingData(
                    team_id: $team->id,
                    team_name: $team->name,
                    played: (int) ($team->played ?? 0),
                    wins: (int) ($team->wins ?? 0),
                    draws: (int) ($team->draws ?? 0),
                    losses: (int) ($team->losses ?? 0),
                    points: (int) ($team->points ?? 0),
                    withdrawn: ! is_null($team->pivot->withdrawn_at ?? null),
                    expelled: (bool) ($team->expelled ?? false),
                );
            });
    }
}
