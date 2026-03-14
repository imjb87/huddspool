<?php

namespace App\Filament\Widgets;

use App\Queries\GetOpenSeasonStats;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected int|array|null $columns = [
        'md' => 3,
        'xl' => 3,
    ];

    protected ?string $heading = 'Open season totals';

    protected ?string $description = 'Current recorded activity for the live season.';

    protected function getStats(): array
    {
        $stats = (new GetOpenSeasonStats)();

        return [
            Stat::make('Active Players', $stats->totalPlayers)
                ->description('Players with recorded frames')
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->color('success'),
            Stat::make('Matches Played', $stats->totalResults)
                ->description('Results recorded in the open season')
                ->descriptionIcon('heroicon-m-clipboard-document-list', IconPosition::Before)
                ->color('info'),
            Stat::make('Frames Played', $stats->totalFrames)
                ->description('Frames recorded in the open season')
                ->descriptionIcon('heroicon-m-bars-3-bottom-left', IconPosition::Before)
                ->color('warning'),
        ];
    }
}
