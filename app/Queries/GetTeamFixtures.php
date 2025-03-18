<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;
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
        return DB::select(
            'SELECT fixtures.id,
                    fixtures.week,
                    fixtures.fixture_date,
                    fixtures.home_team_id,
                    fixtures.away_team_id,
                    home_teams.name AS home_team_name,
                    away_teams.name AS away_team_name,
                    home_teams.shortname AS home_team_shortname,
                    away_teams.shortname AS away_team_shortname,
                    results.id AS result_id,
                    results.home_score,
                    results.away_score
            FROM
                fixtures
            JOIN
                teams AS home_teams ON home_teams.id = fixtures.home_team_id
            JOIN
                teams AS away_teams ON away_teams.id = fixtures.away_team_id
            LEFT OUTER JOIN
                results ON fixtures.id = results.fixture_id
            WHERE
                (fixtures.home_team_id = ' . $this->team->id . ' OR fixtures.away_team_id = ' . $this->team->id . ')
                AND fixtures.section_id = ' . $this->section->id . '
            ORDER BY
                fixtures.week ASC'
        );
    }
}