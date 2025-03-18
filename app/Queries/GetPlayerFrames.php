<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;
use App\Models\Section;
use App\Models\User;

class GetPlayerFrames
{
    public function __construct(
        protected User $player,
        protected Section $section,
    ) {}

    public function __invoke()
    {
        return DB::select(
            'SELECT 
                frames.home_player_id,
                frames.away_player_id,
                home_players.name AS home_player_name,
                away_players.name AS away_player_name,
                frames.home_score,
                frames.away_score,
                results.id AS result_id,
                fixtures.fixture_date,
                home_teams.name AS home_team_name,
                away_teams.name AS away_team_name,
                home_teams.shortname AS home_team_shortname,
                away_teams.shortname AS away_team_shortname
            FROM
                frames
            JOIN
                results ON results.id = frames.result_id
            JOIN
                fixtures ON fixtures.id = results.fixture_id
            JOIN
                teams AS home_teams ON home_teams.id = fixtures.home_team_id
            JOIN
                teams AS away_teams ON away_teams.id = fixtures.away_team_id
            JOIN
                users AS home_players ON home_players.id = frames.home_player_id
            JOIN
                users AS away_players ON away_players.id = frames.away_player_id
            WHERE
                (frames.home_player_id = ' . $this->player->id . ' OR frames.away_player_id = ' . $this->player->id . ')
                AND results.section_id = ' . $this->section->id . '
            ORDER BY
                fixtures.fixture_date DESC'
        );
    }
}