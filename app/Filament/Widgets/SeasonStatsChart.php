<?php

namespace App\Filament\Widgets;

use App\Queries\GetSeasonSeriesStats;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class SeasonStatsChart extends ChartWidget
{
    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Season trends';

    protected ?string $description = 'Last 6 seasons: players, matches, frames.';

    protected ?string $maxHeight = '320px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $series = $this->getSeasonSeries();

        return [
            'labels' => $series['labels'],
            'datasets' => [
                [
                    'label' => 'Players',
                    'data' => $series['players'],
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Matches',
                    'data' => $series['results'],
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Frames',
                    'data' => $series['frames'],
                    'borderColor' => '#F59E0B',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    private function getSeasonSeries(): array
    {
        return Cache::remember('stats:season-series-chart', now()->addMinutes(10), fn (): array => (new GetSeasonSeriesStats)());
    }
}
