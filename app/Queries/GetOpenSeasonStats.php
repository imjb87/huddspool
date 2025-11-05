<?php

namespace App\Queries;

use App\Data\OpenSeasonStatsData;
use App\Models\Frame;
use App\Models\Result;
use Illuminate\Support\Facades\Cache;

class GetOpenSeasonStats
{
    public function __invoke(): OpenSeasonStatsData
    {
        return Cache::remember('stats:open-season', now()->addMinutes(5), function () {
            $frameBaseQuery = Frame::query()
            ->whereHas('result.fixture.season', fn ($query) => $query->where('is_open', true));

            $totalFrames = (clone $frameBaseQuery)->count();

            $homePlayerIds = (clone $frameBaseQuery)
            ->select('home_player_id')
            ->whereNotNull('home_player_id')
            ->distinct()
            ->pluck('home_player_id');

            $awayPlayerIds = (clone $frameBaseQuery)
            ->select('away_player_id')
            ->whereNotNull('away_player_id')
            ->distinct()
            ->pluck('away_player_id');

            $totalPlayers = $homePlayerIds
            ->merge($awayPlayerIds)
            ->filter()
            ->unique()
            ->count();

            $totalResults = Result::query()
            ->whereHas('fixture.season', fn ($query) => $query->where('is_open', true))
            ->count();

            return new OpenSeasonStatsData(
            totalFrames: $totalFrames,
            totalResults: $totalResults,
            totalPlayers: $totalPlayers,
        );
        });
    }
}
