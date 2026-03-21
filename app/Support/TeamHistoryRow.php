<?php

namespace App\Support;

class TeamHistoryRow
{
    /**
     * @param  array<string, mixed>  $entry
     */
    public static function fromEntry(array $entry): object
    {
        return (object) [
            'season_id' => $entry['season_id'],
            'ruleset_id' => $entry['ruleset_id'],
            'season_name' => $entry['season_name'],
            'ruleset_name' => $entry['ruleset_name'] ?? 'Ruleset TBC',
            'played' => $entry['played'],
            'wins' => $entry['wins'],
            'draws' => $entry['draws'],
            'losses' => $entry['losses'],
            'points' => $entry['points'],
            'history_link' => $entry['ruleset_slug']
                ? route('history.show', ['season' => $entry['season_slug'], 'ruleset' => $entry['ruleset_slug']])
                : null,
        ];
    }
}
