<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = DB::select('
            select count(*) as total_frames, count(distinct result_id) as total_results
            from frames as a
            join results as b
            on a.result_id = b.id
            join fixtures as c
            on b.fixture_id = c.id
            join seasons as d
            on c.season_id = d.id and d.is_open = 1            
        ');

        $players = DB::select('
            select count(distinct home_player_id) as total_players
            from frames as a
            join results as b
            on a.result_id = b.id
            join fixtures as c
            on b.fixture_id = c.id
            join seasons as d
            on c.season_id = d.id and d.is_open = 1            
            union all
            select count(distinct away_player_id) as total_players
            from frames as a
            join results as b
            on a.result_id = b.id
            join fixtures as c
            on b.fixture_id = c.id
            join seasons as d
            on c.season_id = d.id and d.is_open = 1
            where away_player_id not in (select distinct home_player_id from frames)
        ');

        $stats[0]->total_players = $players[0]->total_players + $players[1]->total_players;

        return [
            Stat::make('Active Players', $stats[0]->total_players),
            Stat::make('Matches Played', $stats[0]->total_results),
            Stat::make('Frames Played', $stats[0]->total_frames),
        ];
    }
}
