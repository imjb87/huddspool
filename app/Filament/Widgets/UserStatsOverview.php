<?php

namespace App\Filament\Widgets;

use App\Queries\GetOpenSeasonStats;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsOverview extends BaseWidget
{
    protected int|string|array $columnSpan = ['default' => 'full'];

    protected int|array|null $columns = [
        'md' => 3,
        'xl' => 3,
    ];

    protected function getStats(): array
    {
        $stats = (new GetOpenSeasonStats)();

        return [
            Stat::make('Active Players', $stats->totalPlayers)
                ->color('success'),
            Stat::make('Matches Played', $stats->totalResults)
                ->color('info'),
            Stat::make('Frames Played', $stats->totalFrames)
                ->color('warning'),
        ];
    }
}
