<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;
use App\Models\Section;
use App\Models\User;

class GetPlayerAverages
{
    public function __construct(
        protected User $player,
        protected ?Section $section = null,
    ) {}

    public function __invoke()
    {
        return collect(DB::select(
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
                                AND frames.home_score > frames.away_score)
                                OR (users.id = frames.away_player_id
                                    AND frames.away_score > frames.home_score) THEN 1
                            ELSE 0
                        END) / COUNT(frames.id) * 100 AS frames_won_percentage,
                    SUM(CASE
                            WHEN (users.id = frames.home_player_id
                                AND frames.home_score < frames.away_score)
                                OR (users.id = frames.away_player_id
                                    AND frames.away_score < frames.home_score) THEN 1
                            ELSE 0
                        END) AS frames_lost,
                    SUM(CASE
                            WHEN (users.id = frames.home_player_id
                                AND frames.home_score < frames.away_score)
                                OR (users.id = frames.away_player_id
                                    AND frames.away_score < frames.home_score) THEN 1
                            ELSE 0
                        END) / COUNT(frames.id) * 100 AS frames_lost_percentage
            FROM 
                frames
            JOIN 
                users ON frames.home_player_id = users.id OR frames.away_player_id = users.id
            JOIN 
                results ON results.id = frames.result_id
            WHERE 
                results.section_id = ' . $this->section->id . '
                AND users.id = ' . $this->player->id
        ))->first();
    }
}