<?php

namespace App\Data;

final class SectionStandingData
{
    public function __construct(
        public int $team_id,
        public string $team_name,
        public int $played,
        public int $wins,
        public int $draws,
        public int $losses,
        public int $points,
        public bool $withdrawn,
        public bool $expelled,
    ) {
    }
}
