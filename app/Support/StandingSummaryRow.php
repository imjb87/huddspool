<?php

namespace App\Support;

class StandingSummaryRow
{
    public static function fromStanding(object $team, int $position, bool $isHistoryView): object
    {
        $withdrawn = (bool) ($team->pivot->withdrawn_at ?? false);
        $displayName = $isHistoryView ? ($team->archived_name ?? $team->name) : $team->name;
        $textClass = $withdrawn ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-900 dark:text-gray-100';

        return (object) [
            'id' => $team->id,
            'position' => $position,
            'played' => $team->played,
            'wins' => $team->wins,
            'draws' => $team->draws,
            'losses' => $team->losses,
            'points' => $team->points,
            'name' => $displayName,
            'shortname' => $isHistoryView ? ($team->archived_name ?? $team->shortname) : $team->shortname,
            'withdrawn' => $withdrawn,
            'text_class' => $textClass,
            'points_class' => $withdrawn ? 'text-gray-400 dark:text-neutral-500' : 'text-green-700 dark:text-green-400',
            'can_link' => ! ($isHistoryView && ($team->trashed ?? false)),
        ];
    }
}
