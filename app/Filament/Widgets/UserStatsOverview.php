<?php

namespace App\Filament\Widgets;

use App\Queries\GetOpenSeasonStats;
use App\Models\Season;
use App\Models\Frame;
use App\Models\Result;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class UserStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = (new GetOpenSeasonStats())();
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
        return Cache::remember('stats:season-series', now()->addMinutes(10), function () {
            $seasons = Season::query()
                ->orderByDesc('id')
                ->limit(6)
                ->get()
                ->reverse();

            $players = [];
            $results = [];
            $frames = [];

            foreach ($seasons as $season) {
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
                'players' => $players,
                'results' => $results,
                'frames' => $frames,
            ];
        });
    }
}
