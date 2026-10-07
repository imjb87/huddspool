<?php

namespace App\Support;

class ResultFormCollaboratorColor
{
    /**
     * @var array<int, string>
     */
    private const COLORS = [
        '#2563eb',
        '#7c3aed',
        '#0f766e',
        '#c2410c',
        '#be185d',
        '#4d7c0f',
        '#0369a1',
        '#a16207',
    ];

    /**
     * @return array<int, string>
     */
    public static function palette(): array
    {
        return self::COLORS;
    }

    /**
     * @param  array<int, int|string>  $userIds
     */
    public static function forRoster(array $userIds, int $userId): string
    {
        $sortedUserIds = array_values(array_unique(array_map(
            static fn (int|string $id): int => (int) $id,
            $userIds,
        )));

        sort($sortedUserIds, SORT_NUMERIC);

        $rosterIndex = array_search($userId, $sortedUserIds, true);

        return $rosterIndex === false
            ? self::forUser($userId)
            : self::forIndex((int) $rosterIndex);
    }

    public static function forUser(int $userId): string
    {
        return self::forIndex(max($userId - 1, 0));
    }

    private static function forIndex(int $index): string
    {
        if (isset(self::COLORS[$index])) {
            return self::COLORS[$index];
        }

        $colors = self::COLORS;
        $candidateIndex = 0;

        while (count($colors) <= $index) {
            $candidate = sprintf('#%06x', (0x123456 + ($candidateIndex * 0x9E3779)) % 0x1000000);
            $candidateIndex++;

            if (! in_array($candidate, $colors, true)) {
                $colors[] = $candidate;
            }
        }

        return $colors[$index];
    }
}
