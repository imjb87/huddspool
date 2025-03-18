<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;
use App\Models\Section;

class GetSectionAverages
{
    public function __construct(
        protected Section $section,
        protected int $page = 1,
        protected int $perPage = 10
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
                frames
            JOIN 
                users ON frames.home_player_id = users.id OR frames.away_player_id = users.id
            JOIN 
                results ON results.id = frames.result_id
            WHERE 
                results.section_id = ' . $this->section->id . '
                AND users.id NOT IN
                    (SELECT expellable_id
                    FROM expulsions
                    WHERE expellable_type = "App\\\Models\\\User"
                        AND season_id = ' . $this->section->id . ')
            GROUP BY 
                users.id
            ORDER BY 
                frames_won DESC,
                frames_lost ASC,
                users.name ASC
            LIMIT 
                ' . $this->perPage . '
            OFFSET 
                ' . ($this->page - 1) * $this->perPage
        );
    }
}
