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
}
