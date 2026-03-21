<?php

namespace App\Support;

class FrameSummaryRow
{
    public static function fromFrame(object $frame, int $playerId): object
    {
        $wonFrame = $frame->home_player_id === $playerId
            ? $frame->home_score > $frame->away_score
            : $frame->away_score > $frame->home_score;

        return (object) [
            'result_id' => $frame->result_id,
            'won_frame' => $wonFrame,
            'result_pill_classes' => $wonFrame
                ? 'bg-linear-to-br from-green-900 via-green-800 to-green-700'
                : 'bg-linear-to-br from-red-800 via-red-700 to-red-600',
            'opponent_name' => $frame->home_player_id === $playerId ? $frame->away_player_name : $frame->home_player_name,
            'opponent_team' => $frame->home_player_id === $playerId ? $frame->away_team_name : $frame->home_team_name,
            'fixture_date_label' => $frame->fixture_date->format('j M'),
        ];
    }
}
