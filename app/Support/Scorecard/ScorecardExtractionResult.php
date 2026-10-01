<?php

namespace App\Support\Scorecard;

class ScorecardExtractionResult
{
    /**
     * @param  array<int, array{home_player_name: string|null, away_player_name: string|null, home_score: int, away_score: int}>  $frames
     * @param  list<string>  $warnings
     */
    public function __construct(
        public readonly array $frames,
        public readonly array $warnings = [],
    ) {}
}
