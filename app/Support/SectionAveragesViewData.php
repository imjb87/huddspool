<?php

namespace App\Support;

use Illuminate\Support\Collection;

class SectionAveragesViewData
{
    /**
     * @return array{
     *     summaryCopy: string,
     *     lastPage: int,
     *     averageRows: Collection<int, array{
     *         player: mixed,
     *         can_link: bool,
     *         ranking: int
     *     }>
     * }
     */
    public static function make(
        Collection $players,
        int $page,
        int $perPage,
        bool $isHistoryView,
        ?int $totalPlayers = null,
    ): array {
        $resolvedTotalPlayers = $totalPlayers ?? $players->count();

        return [
            'summaryCopy' => $isHistoryView
                ? 'Archived frame records and win rates for this section.'
                : 'Current frame records and win rates for this section.',
            'lastPage' => max(1, (int) ceil($resolvedTotalPlayers / $perPage)),
            'averageRows' => $players->values()->map(fn ($player, $index) => [
                'player' => $player,
                'can_link' => ! ($isHistoryView && $player->trashed),
                'ranking' => $index + 1 + (($page - 1) * $perPage),
            ]),
        ];
    }
}
