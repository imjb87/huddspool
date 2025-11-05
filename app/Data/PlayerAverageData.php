<?php

namespace App\Data;

final class PlayerAverageData
{
    public function __construct(
        public int $id,
        public string $name,
        public int $frames_played,
        public int $frames_won,
        public float $frames_won_percentage,
        public int $frames_lost,
        public float $frames_lost_percentage,
    ) {
    }

    public static function empty(int $id, string $name): self
    {
        return new self(
            id: $id,
            name: $name,
            frames_played: 0,
            frames_won: 0,
            frames_won_percentage: 0.0,
            frames_lost: 0,
            frames_lost_percentage: 0.0,
        );
    }
}
