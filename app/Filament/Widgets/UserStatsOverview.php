<?php

namespace App\Filament\Widgets;

use App\Queries\GetOpenSeasonStats;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = (new GetOpenSeasonStats())();

        return [
            Stat::make('Active Players', $stats->totalPlayers)->chart([5, 12, 8, 19, 4, 13, 9])->color('primary'),
            Stat::make('Matches Played', $stats->totalResults)->chart([2, 7, 3, 6, 11, 4, 5])->color('primary'),
            Stat::make('Frames Played', $stats->totalFrames)->chart([8, 3, 11, 6, 9, 5, 7])->color('primary')
        ];
    }
}
