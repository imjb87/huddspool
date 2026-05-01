<?php

namespace App\Support\Scorecard;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ScorecardPlayerResolver
{
    /**
     * Maximum edit-distance ratio (percentage of name length) allowed for a fuzzy match.
     */
    private const int MAX_DISTANCE_RATIO = 40;

    /**
     * Attempt to resolve a player name string against a collection of eligible players.
     *
     * Returns the matched player's ID, or null with a warning when no confident match is found.
     *
     * @param  Collection<int, User>  $players
     * @return array{id: int|null, warning: string|null}
     */
    public function resolve(string $name, Collection $players, int $frameNumber, string $side): array
    {
        $name = trim($name);

        if ($name === '') {
            return ['id' => null, 'warning' => null];
        }

        // Exact case-insensitive match first.
        foreach ($players as $player) {
            if (strtolower($player->name) === strtolower($name)) {
                return ['id' => $player->id, 'warning' => null];
            }
        }

        // Fuzzy match via Levenshtein distance.
        $best = null;
        $bestDistance = PHP_INT_MAX;

        foreach ($players as $player) {
            $distance = levenshtein(strtolower($name), strtolower($player->name));
            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $best = $player;
            }
        }

        if ($best !== null) {
            $maxAllowed = (int) ceil(strlen($name) * self::MAX_DISTANCE_RATIO / 100);
            if ($bestDistance <= $maxAllowed) {
                return ['id' => $best->id, 'warning' => null];
            }
        }

        $label = ucfirst($side);

        return [
            'id' => null,
            'warning' => "Frame {$frameNumber}: {$label} player \"{$name}\" could not be matched — please select manually.",
        ];
    }
}
