<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;
use App\Models\Team;
use App\Models\Section;

class GetTeamPlayers
{
    public function __construct(
        protected Team $team,
        protected Section $section,
    ) {}

    public function __invoke()
    {
        return DB::select(
            'SELECT users.id,
                    users.name,
                    COUNT(frames.id) AS frames_played,
                    SUM(CASE
                            WHEN (users.id = frames.home_player_id
                                AND frames.home_score > frames.away_score)
                                OR (users.id = frames.away_player_id
                                    AND frames.away_score > frames.home_score) THEN 1
                            ELSE 0
                        END) AS frames_won,
                    SUM(CASE
                            WHEN (users.id = frames.home_player_id
                                AND frames.home_score < frames.away_score)
                                OR (users.id = frames.away_player_id
                                    AND frames.away_score < frames.home_score) THEN 1
                            ELSE 0
                        END) AS frames_lost
            FROM
                users
            JOIN 
                teams ON teams.id = users.team_id
            LEFT OUTER JOIN
                frames ON (frames.home_player_id = users.id OR frames.away_player_id = users.id) AND frames.result_id IN (
                    SELECT
                        results.id
                    FROM
                        results
                    WHERE
                        results.section_id = ' . $this->section->id . '
                )
            WHERE
                teams.id = ' . $this->team->id . '
            GROUP BY
                users.id
            ORDER BY
                users.name ASC'
        );
    }
}
