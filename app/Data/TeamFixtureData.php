<?php

namespace App\Data;

use App\Models\Fixture;
use Illuminate\Support\Carbon;

final class TeamFixtureData
{
    public function __construct(
        public int $id,
        public ?int $week,
        public ?Carbon $fixture_date,
        public int $home_team_id,
        public int $away_team_id,
        public ?string $home_team_name,
        public ?string $away_team_name,
        public ?string $home_team_shortname,
        public ?string $away_team_shortname,
        public ?int $result_id,
        public ?int $home_score,
        public ?int $away_score,
    ) {
    }

    public static function fromFixture(Fixture $fixture): self
    {
        return new self(
            id: $fixture->id,
            week: $fixture->week,
            fixture_date: $fixture->fixture_date,
            home_team_id: $fixture->home_team_id,
            away_team_id: $fixture->away_team_id,
            home_team_name: $fixture->homeTeam?->name,
            away_team_name: $fixture->awayTeam?->name,
            home_team_shortname: $fixture->homeTeam?->shortname,
            away_team_shortname: $fixture->awayTeam?->shortname,
            result_id: $fixture->result?->id,
            home_score: $fixture->result?->home_score,
            away_score: $fixture->result?->away_score,
        );
    }
}
