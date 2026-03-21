<?php

namespace App\Enums;

enum RoleName: string
{
    case Admin = 'admin';
    case TeamAdmin = 'team-admin';
    case Player = 'player';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::TeamAdmin => 'Team Admin',
            self::Player => 'Player',
        };
    }
}
