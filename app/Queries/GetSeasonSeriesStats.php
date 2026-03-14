<?php

namespace App\Queries;

use App\Models\Frame;
use App\Models\Result;
use App\Models\Season;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GetSeasonSeriesStats
{
    private const string SEASON_ID_SQL = 'COALESCE(fixtures.season_id, sections.season_id)';

    /**
     * @return array{
     *     labels: array<int, string>,
     *     players: array<int, int>,
     *     results: array<int, int>,
     *     frames: array<int, int>
     * }
     */
    public function __invoke(int $limit = 6): array
    {
        $seasons = Season::query()
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();

        if ($seasons->isEmpty()) {
            return [
                'labels' => [],
                'players' => [],
                'results' => [],
                'frames' => [],
            ];
        }

        $seasonIds = $seasons->pluck('id')->all();

        $playerCounts = $this->playerCountsForSeasons($seasonIds);
        $resultCounts = $this->resultCountsForSeasons($seasonIds);
        $frameCounts = $this->frameCountsForSeasons($seasonIds);

        return [
            'labels' => $seasons
                ->map(fn (Season $season): string => $season->name ?: 'Season '.$season->id)
                ->all(),
            'players' => $this->valuesForSeasons($seasons, $playerCounts),
            'results' => $this->valuesForSeasons($seasons, $resultCounts),
            'frames' => $this->valuesForSeasons($seasons, $frameCounts),
        ];
    }

    /**
     * @param  array<int, int>  $seasonIds
     * @return Collection<int, int>
     */
    private function playerCountsForSeasons(array $seasonIds): Collection
    {
        $homePlayers = $this->frameSeasonQuery($seasonIds)
            ->selectRaw(self::SEASON_ID_SQL.' as season_id, frames.home_player_id as player_id')
            ->whereNotNull('frames.home_player_id');

        $awayPlayers = $this->frameSeasonQuery($seasonIds)
            ->selectRaw(self::SEASON_ID_SQL.' as season_id, frames.away_player_id as player_id')
            ->whereNotNull('frames.away_player_id');

        return DB::query()
            ->fromSub($homePlayers->unionAll($awayPlayers), 'season_players')
            ->select('season_id')
            ->selectRaw('COUNT(DISTINCT player_id) as aggregate')
            ->groupBy('season_id')
            ->pluck('aggregate', 'season_id')
            ->map(fn (mixed $count): int => (int) $count);
    }

    /**
     * @param  array<int, int>  $seasonIds
     * @return Collection<int, int>
     */
    private function resultCountsForSeasons(array $seasonIds): Collection
    {
        return Result::query()
            ->leftJoin('fixtures', 'fixtures.id', '=', 'results.fixture_id')
            ->leftJoin('sections', function (JoinClause $join): void {
                $join->on('sections.id', '=', 'results.section_id')
                    ->whereNull('sections.deleted_at');
            })
            ->where(function (Builder $query) use ($seasonIds): void {
                $query->whereIn('fixtures.season_id', $seasonIds)
                    ->orWhereIn('sections.season_id', $seasonIds);
            })
            ->selectRaw(self::SEASON_ID_SQL.' as season_id')
            ->selectRaw('COUNT(results.id) as aggregate')
            ->groupByRaw(self::SEASON_ID_SQL)
            ->pluck('aggregate', 'season_id')
            ->map(fn (mixed $count): int => (int) $count);
    }

    /**
     * @param  array<int, int>  $seasonIds
     * @return Collection<int, int>
     */
    private function frameCountsForSeasons(array $seasonIds): Collection
    {
        return $this->frameSeasonQuery($seasonIds)
            ->selectRaw(self::SEASON_ID_SQL.' as season_id')
            ->selectRaw('COUNT(frames.id) as aggregate')
            ->groupByRaw(self::SEASON_ID_SQL)
            ->pluck('aggregate', 'season_id')
            ->map(fn (mixed $count): int => (int) $count);
    }

    /**
     * @param  array<int, int>  $seasonIds
     */
    private function frameSeasonQuery(array $seasonIds): Builder
    {
        return Frame::query()
            ->join('results', function (JoinClause $join): void {
                $join->on('results.id', '=', 'frames.result_id')
                    ->whereNull('results.deleted_at');
            })
            ->leftJoin('fixtures', 'fixtures.id', '=', 'results.fixture_id')
            ->leftJoin('sections', function (JoinClause $join): void {
                $join->on('sections.id', '=', 'results.section_id')
                    ->whereNull('sections.deleted_at');
            })
            ->where(function (Builder $query) use ($seasonIds): void {
                $query->whereIn('fixtures.season_id', $seasonIds)
                    ->orWhereIn('sections.season_id', $seasonIds);
            });
    }

    /**
     * @param  Collection<int, Season>  $seasons
     * @param  Collection<int, int>  $counts
     * @return array<int, int>
     */
    private function valuesForSeasons(Collection $seasons, Collection $counts): array
    {
        return $seasons
            ->map(fn (Season $season): int => (int) ($counts->get($season->id) ?? 0))
            ->all();
    }
}
