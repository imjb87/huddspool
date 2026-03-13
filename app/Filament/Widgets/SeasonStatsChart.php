<?php

namespace App\Filament\Widgets;

use App\Models\Frame;
use App\Models\Result;
use App\Models\Season;
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
        return Cache::remember('stats:season-series-chart', now()->addMinutes(10), function () {
            $seasons = Season::query()
                ->orderByDesc('id')
                ->limit(6)
                ->get()
                ->reverse();

            $labels = [];
            $players = [];
            $results = [];
            $frames = [];

            foreach ($seasons as $season) {
                $labels[] = $season->name ?: 'Season '.$season->id;
                $seasonId = $season->id;

                $frameBase = Frame::query()
                    ->whereHas('result', function ($query) use ($seasonId) {
                        $query->whereHas('fixture', fn ($q) => $q->where('season_id', $seasonId))
                            ->orWhereHas('section', fn ($q) => $q->where('season_id', $seasonId));
                    });

                $homePlayerIds = (clone $frameBase)
                    ->whereNotNull('home_player_id')
                    ->distinct()
                    ->pluck('home_player_id');

                $awayPlayerIds = (clone $frameBase)
                    ->whereNotNull('away_player_id')
                    ->distinct()
                    ->pluck('away_player_id');

                $players[] = $homePlayerIds
                    ->merge($awayPlayerIds)
                    ->filter()
                    ->unique()
                    ->count();

                $frames[] = (clone $frameBase)->count();

                $results[] = Result::query()
                    ->where(function ($query) use ($seasonId) {
                        $query->whereHas('fixture', fn ($q) => $q->where('season_id', $seasonId))
                            ->orWhereHas('section', fn ($q) => $q->where('season_id', $seasonId));
                    })
                    ->count();
            }

            return [
                'labels' => $labels,
                'players' => $players,
                'results' => $results,
                'frames' => $frames,
            ];
        });
    }
}
