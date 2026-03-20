<?php

namespace App\Support;

use App\Models\Fixture;
use App\Models\User;

class ResultFormFrameRowBuilder
{
    /**
     * @param  array<int, array{home_player_id?: int|string|null, away_player_id?: int|string|null}>  $frames
     * @return array<int, array{
     *     number: int,
     *     home_selected_player: ?User,
     *     away_selected_player: ?User,
     *     home_is_awarded: bool,
     *     away_is_awarded: bool
     * }>
     */
    public function build(Fixture $fixture, array $frames): array
    {
        $rows = [];

        for ($i = 1; $i <= 10; $i++) {
            $homePlayerId = (int) data_get($frames, $i.'.home_player_id');
            $awayPlayerId = (int) data_get($frames, $i.'.away_player_id');

            $rows[] = [
                'number' => $i,
                'home_selected_player' => $fixture->homeTeam->players->firstWhere('id', $homePlayerId),
                'away_selected_player' => $fixture->awayTeam->players->firstWhere('id', $awayPlayerId),
                'home_is_awarded' => (string) data_get($frames, $i.'.home_player_id') === '0',
                'away_is_awarded' => (string) data_get($frames, $i.'.away_player_id') === '0',
            ];
        }

        return $rows;
    }
}
