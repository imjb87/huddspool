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
                ? 'ui-score-pill-success'
                : 'ui-score-pill-danger',
            'opponent_name' => $frame->home_player_id === $playerId ? $frame->away_player_name : $frame->home_player_name,
            'opponent_team' => $frame->home_player_id === $playerId ? $frame->away_team_name : $frame->home_team_name,
            'fixture_date_label' => $frame->fixture_date->format('j M'),
        ];
    }
}
