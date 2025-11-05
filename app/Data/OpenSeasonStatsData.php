<?php

namespace App\Data;

final class OpenSeasonStatsData
{
    public function __construct(
        public int $totalFrames,
        public int $totalResults,
        public int $totalPlayers,
    ) {
    }
}
