<?php

namespace App\Filament\Widgets;

use App\Queries\GetSeasonSeriesStats;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SeasonStatsChart extends ChartWidget
{
    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = '3 season trend';

    protected ?string $description = 'Players, matches, and frames shown side by side across the last 3 seasons.';

    protected ?string $maxHeight = '320px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $series = $this->getSeasonSeries();
        $metrics = $this->getMetricDefinitions();

        return [
            'labels' => $this->shortLabels($series['labels']),
            'datasets' => array_map(function (array $metric) use ($series): array {
                return [
                    'label' => $metric['label'],
                    'data' => $series[$metric['key']],
                    'backgroundColor' => $metric['backgroundColor'],
                    'borderColor' => $metric['borderColor'],
                    'borderWidth' => 1,
                    'borderRadius' => 8,
                    'maxBarThickness' => 22,
                    'categoryPercentage' => 0.72,
                    'barPercentage' => 0.88,
                ];
            }, $metrics),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'displayColors' => false,
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'maxRotation' => 0,
                        'minRotation' => 0,
                    ],
                ],
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

    /**
     * @return array<int, array{key: string, label: string, backgroundColor: string, borderColor: string}>
     */
    private function getMetricDefinitions(): array
    {
        return [
            [
                'key' => 'players',
                'label' => 'Players',
                'backgroundColor' => 'rgba(16, 185, 129, 0.85)',
                'borderColor' => '#10B981',
            ],
            [
                'key' => 'results',
                'label' => 'Matches',
                'backgroundColor' => 'rgba(59, 130, 246, 0.85)',
                'borderColor' => '#3B82F6',
            ],
            [
                'key' => 'frames',
                'label' => 'Frames',
                'backgroundColor' => 'rgba(245, 158, 11, 0.85)',
                'borderColor' => '#F59E0B',
            ],
        ];
    }

    /**
     * @param  array<int, string>  $labels
     * @return array<int, string>
     */
    private function shortLabels(array $labels): array
    {
        return array_map(
            fn (string $label): string => (string) Str::of($label)->squish()->limit(10, '…'),
            $labels,
        );
    }
}
