<?php

namespace App\Queries;

use App\Data\OpenSeasonStatsData;
use App\Models\Frame;
use App\Models\Result;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GetOpenSeasonStats
{
    public function __invoke(): OpenSeasonStatsData
    {
        return Cache::remember('stats:open-season', now()->addMinutes(5), function () {
            $frameBaseQuery = Frame::query()
                ->join('results', 'results.id', '=', 'frames.result_id')
                ->join('fixtures', 'fixtures.id', '=', 'results.fixture_id')
                ->join('seasons', 'seasons.id', '=', 'fixtures.season_id')
                ->where('seasons.is_open', true);

            $totalFrames = (clone $frameBaseQuery)->count();

            $homePlayerIds = (clone $frameBaseQuery)
                ->select('frames.home_player_id as player_id')
                ->whereNotNull('frames.home_player_id');

            $awayPlayerIds = (clone $frameBaseQuery)
                ->select('frames.away_player_id as player_id')
                ->whereNotNull('frames.away_player_id');

            $totalPlayers = DB::query()
                ->fromSub($homePlayerIds->union($awayPlayerIds), 'open_season_players')
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
