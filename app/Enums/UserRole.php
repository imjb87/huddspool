<?php

namespace App\Enums;

enum UserRole: string
{
    case Player = '1';
    case TeamAdmin = '2';

    public function label(): string
    {
        return match ($this) {
            self::Player => 'Player',
            self::TeamAdmin => 'Team Admin',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $role) {
            $options[$role->value] = $role->label();
        }

        return $options;
    }

    public static function labelFor(string|int|null $value): string
    {
        return self::tryFrom((string) $value)?->label() ?? self::Player->label();
    }
}
