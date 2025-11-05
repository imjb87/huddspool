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
    ) {
    }

    public function __invoke(): PlayerAverageData
    {
        $frames = Frame::query()
            ->select('frames.*', 'results.section_id')
            ->join('results', 'results.id', '=', 'frames.result_id')
            ->where(function ($builder) {
                $builder
                    ->where('frames.home_player_id', $this->player->id)
                    ->orWhere('frames.away_player_id', $this->player->id);
            })
            ->when($this->section, function ($builder) {
                $builder->where('results.section_id', $this->section->id);
            })
            ->get();

        if ($frames->isEmpty()) {
            return PlayerAverageData::empty($this->player->id, $this->player->name);
        }

        $framesPlayed = $frames->count();

        $framesWon = $frames->filter(function (Frame $frame) {
            $playerIsHome = $frame->home_player_id === $this->player->id;

            return $playerIsHome
                ? $frame->home_score > $frame->away_score
                : $frame->away_score > $frame->home_score;
        })->count();

        $framesLost = $frames->filter(function (Frame $frame) {
            $playerIsHome = $frame->home_player_id === $this->player->id;

            return $playerIsHome
                ? $frame->home_score < $frame->away_score
                : $frame->away_score < $frame->home_score;
        })->count();

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
