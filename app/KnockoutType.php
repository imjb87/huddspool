<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum KnockoutType: string implements HasLabel
{
    case Singles = 'singles';
    case Doubles = 'doubles';
    case Team = 'team';

    public function getLabel(): string
    {
        return match ($this) {
            self::Singles => 'Singles',
            self::Doubles => 'Doubles',
            self::Team => 'Team',
        };
    }

    public function requiresBestOf(): bool
    {
        return $this !== self::Team;
    }

    public function defaultBestOf(): int
    {
    return $this === self::Team ? 11 : 5;
    }

    public function participantsLabel(): string
    {
        return match ($this) {
            self::Singles => 'Player',
            self::Doubles => 'Pair',
            self::Team => 'Team',
        };
    }

    public function maxScoreAllowed(): int
    {
    return $this === self::Team ? 11 : 9;
    }
}
