<?php

namespace App\Filament\Widgets;

use App\Queries\GetOpenSeasonStats;
use App\Queries\GetSeasonSeriesStats;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class UserStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = (new GetOpenSeasonStats)();
        $series = $this->getSeasonSeries();

        return [
            Stat::make('Active Players', $stats->totalPlayers)
                ->chart($series['players'])
                ->color('primary'),
            Stat::make('Matches Played', $stats->totalResults)
                ->chart($series['results'])
                ->color('primary'),
            Stat::make('Frames Played', $stats->totalFrames)
                ->chart($series['frames'])
                ->color('primary'),
        ];
    }

    private function getSeasonSeries(): array
    {
        return Cache::remember('stats:season-series', now()->addMinutes(10), fn (): array => (new GetSeasonSeriesStats)());
    }
}
