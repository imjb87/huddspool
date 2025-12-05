<?php

namespace App\Data;

final class SectionPlayerAverageData
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $team_name,
        public int $frames_played,
        public int $frames_won,
        public int $frames_lost,
        public float $frames_won_percentage,
        public float $frames_lost_percentage,
        public string $avatar_url,
    ) {
    }
}
