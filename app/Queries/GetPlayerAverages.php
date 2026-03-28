<?php

namespace App\Queries;

use App\Data\PlayerAverageData;
use App\Models\Frame;
use App\Models\Section;
use App\Models\User;

class GetPlayerAverages
{
    public function __construct(
        protected User $player,
        protected ?Section $section = null,
    ) {}

    public function __invoke(): PlayerAverageData
    {
        $playerId = $this->player->id;

        $totals = Frame::query()
            ->join('results', 'results.id', '=', 'frames.result_id')
            ->where(function ($builder) {
                $builder
                    ->where('frames.home_player_id', $this->player->id)
                    ->orWhere('frames.away_player_id', $this->player->id);
            })
            ->when($this->section, function ($builder) {
                $builder->where('results.section_id', $this->section->id);
            })
            ->selectRaw('COUNT(*) as frames_played')
            ->selectRaw(
                'SUM(CASE
                    WHEN frames.home_player_id = ? AND frames.home_score > frames.away_score THEN 1
                    WHEN frames.away_player_id = ? AND frames.away_score > frames.home_score THEN 1
                    ELSE 0
                END) as frames_won',
                [$playerId, $playerId],
            )
            ->selectRaw(
                'SUM(CASE
                    WHEN frames.home_player_id = ? AND frames.home_score < frames.away_score THEN 1
                    WHEN frames.away_player_id = ? AND frames.away_score < frames.home_score THEN 1
                    ELSE 0
                END) as frames_lost',
                [$playerId, $playerId],
            )
            ->first();

        $framesPlayed = (int) ($totals?->frames_played ?? 0);

        if ($framesPlayed === 0) {
            return PlayerAverageData::empty($this->player->id, $this->player->name);
        }

        $framesWon = (int) ($totals?->frames_won ?? 0);
        $framesLost = (int) ($totals?->frames_lost ?? 0);

        $winPercentage = $framesPlayed > 0
            ? round(($framesWon / $framesPlayed) * 100, 2)
            : 0.0;

        $lossPercentage = $framesPlayed > 0
            ? round(($framesLost / $framesPlayed) * 100, 2)
            : 0.0;

        return new PlayerAverageData(
            id: $this->player->id,
            name: $this->player->name,
            frames_played: $framesPlayed,
            frames_won: $framesWon,
            frames_won_percentage: $winPercentage,
            frames_lost: $framesLost,
            frames_lost_percentage: $lossPercentage,
        );
    }
}
