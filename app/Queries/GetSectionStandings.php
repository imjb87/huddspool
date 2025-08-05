<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;
use App\Models\Section;

class GetSectionAverages
{
    public function __construct(
        protected Section $section,
    ) {}

    public function __invoke()
    {
        return DB::select(
            'SELECT team.id,
                    team.name,
                    COUNT(results.id) AS played,
                    SUM(CASE
                            WHEN (team.id = results.home_team_id
                                AND results.home_score > results.away_score)
                                OR (team.id = results.away_team_id
                                    AND results.away_score > results.home_score) THEN 1
                            ELSE 0
                        END) AS wins,
                    SUM(CASE
                            WHEN (team.id = results.home_team_id
                                AND results.home_score < results.away_score)
                                OR (team.id = results.away_team_id
                                    AND results.away_score < results.home_score) THEN 1
                            ELSE 0
                        END) AS losses,
                    SUM(CASE
                            WHEN (team.id = results.home_team_id
                                AND results.home_score = results.away_score)
                                OR (team.id = results.away_team_id
                                    AND results.away_score = results.home_score) THEN 1
                            ELSE 0
                        END) AS drawn,
                    SUM(CASE
                            WHEN (team.id = results.home_team_id THEN results.home_score
                            ELSE results.away_score END) AS points,
                    section_team.withdrawn_at,
                    
                FROM 
                    results
                JOIN
                    teams AS team ON results.home_team_id = team.id OR results.away_team_id = team.id
                JOIN
                    section_team ON team.id = section_team.team_id AND section_team.section_id = ' . $this->section->id . '                    
                WHERE 
                    results.section_id = ' . $this->section->id . '
                    AND team.id NOT IN
                        (SELECT expellable_id
                        FROM expulsions
                        WHERE expellable_type = "App\\\Models\\\Team"
                            AND season_id = ' . $this->section->id . ')
                GROUP BY
                    team.id
                ORDER BY
                    won DESC,
                    lost ASC,
                    drawn ASC,
                    team.name ASC'
        );
    }
}
