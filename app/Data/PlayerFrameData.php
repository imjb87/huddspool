<?php

namespace App\Data;

use App\Models\Frame;
use Illuminate\Support\Carbon;

final class PlayerFrameData
{
    public function __construct(
        public int $home_player_id,
        public int $away_player_id,
        public string $home_player_name,
        public string $away_player_name,
        public int $home_score,
        public int $away_score,
        public int $result_id,
        public Carbon $fixture_date,
        public ?string $home_team_name,
        public ?string $away_team_name,
        public ?string $home_team_shortname,
        public ?string $away_team_shortname,
    ) {
    }

    public static function fromFrame(Frame $frame): self
    {
        $fixture = $frame->result->fixture;

        return new self(
            home_player_id: $frame->home_player_id,
            away_player_id: $frame->away_player_id,
            home_player_name: $frame->homePlayer?->name ?? '',
            away_player_name: $frame->awayPlayer?->name ?? '',
            home_score: $frame->home_score,
            away_score: $frame->away_score,
            result_id: $frame->result_id,
            fixture_date: $fixture->fixture_date,
            home_team_name: $fixture->homeTeam?->name,
            away_team_name: $fixture->awayTeam?->name,
            home_team_shortname: $fixture->homeTeam?->shortname,
            away_team_shortname: $fixture->awayTeam?->shortname,
        );
    }
}
