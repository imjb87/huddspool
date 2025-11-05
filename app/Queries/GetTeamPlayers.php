<?php

namespace App\Queries;

use App\Models\Section;
use App\Models\Team;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

class GetTeamPlayers
{
    public function __construct(
        protected Team $team,
        protected ?Section $section,
    ) {
    }

    public function __invoke(): Collection
    {
        $sectionId = $this->section?->id;

        $query = $this->team->players()
            ->select('users.id', 'users.name')
            ->leftJoin('frames', function (JoinClause $join) {
                $join->on('frames.home_player_id', '=', 'users.id')
                    ->orOn('frames.away_player_id', '=', 'users.id');
            })
            ->leftJoin('results', 'results.id', '=', 'frames.result_id')
            ->groupBy('users.id', 'users.name')
            ->orderBy('users.name');

        if ($sectionId) {
            $query->selectRaw(
                'COALESCE(SUM(CASE WHEN results.section_id = ? THEN 1 ELSE 0 END), 0) AS frames_played',
                [$sectionId]
            )->selectRaw(
                'COALESCE(SUM(CASE WHEN results.section_id = ? AND ((frames.home_player_id = users.id AND frames.home_score > frames.away_score) OR (frames.away_player_id = users.id AND frames.away_score > frames.home_score)) THEN 1 ELSE 0 END), 0) AS frames_won',
                [$sectionId]
            )->selectRaw(
                'COALESCE(SUM(CASE WHEN results.section_id = ? AND ((frames.home_player_id = users.id AND frames.home_score < frames.away_score) OR (frames.away_player_id = users.id AND frames.away_score < frames.home_score)) THEN 1 ELSE 0 END), 0) AS frames_lost',
                [$sectionId]
            );
        } else {
            $query->selectRaw(
                'COALESCE(SUM(CASE WHEN results.id IS NOT NULL THEN 1 ELSE 0 END), 0) AS frames_played'
            )->selectRaw(
                'COALESCE(SUM(CASE WHEN (frames.home_player_id = users.id AND frames.home_score > frames.away_score) OR (frames.away_player_id = users.id AND frames.away_score > frames.home_score) THEN 1 ELSE 0 END), 0) AS frames_won'
            )->selectRaw(
                'COALESCE(SUM(CASE WHEN (frames.home_player_id = users.id AND frames.home_score < frames.away_score) OR (frames.away_player_id = users.id AND frames.away_score < frames.home_score) THEN 1 ELSE 0 END), 0) AS frames_lost'
            );
        }

        return $query->get();
    }
}
