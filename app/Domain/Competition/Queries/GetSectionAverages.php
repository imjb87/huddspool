<?php

namespace App\Domain\Competition\Queries;

use App\Data\SectionPlayerAverageData;
use App\Models\Expulsion;
use App\Models\Frame;
use App\Models\Section;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GetSectionAverages
{
    public function __construct(
        protected Section $section,
        protected int $page = 1,
        protected int $perPage = 10
    ) {}

    /**
     * @return Collection<int, SectionPlayerAverageData>
     */
    public function __invoke(): Collection
    {
        $allPlayers = $this->allPlayers();
        $offset = max(0, ($this->page - 1) * $this->perPage);

        return $allPlayers
            ->slice($offset, $this->perPage)
            ->values();
    }

    public function total(): int
    {
        return $this->allPlayers()->count();
    }

    /**
     * @return Collection<int, SectionPlayerAverageData>
     */
    private function allPlayers(): Collection
    {
        $sectionId = $this->section->id;

        return Cache::remember($this->cacheKey($sectionId), now()->addMinutes(2), function () use ($sectionId) {
            $homeAppearances = Frame::query()
                ->join('results', 'results.id', '=', 'frames.result_id')
                ->leftJoin('users as player_users', 'player_users.id', '=', 'frames.home_player_id')
                ->where('results.section_id', $sectionId)
                ->whereNull('results.deleted_at')
                ->where('results.is_confirmed', true)
                ->whereNotNull('frames.home_player_id')
                ->where('frames.home_player_id', '<>', 0)
                ->where(function ($query) {
                    $query->whereNull('frames.away_player_id')
                        ->orWhere('frames.away_player_id', '<>', 0);
                })
                ->selectRaw(<<<'SQL'
                    frames.home_player_id as player_id,
                    player_users.name as player_name,
                    results.home_team_id as team_id,
                    results.home_team_name as team_name,
                    COUNT(*) as frames_played,
                    SUM(CASE WHEN frames.away_player_id IS NOT NULL AND frames.home_score > frames.away_score THEN 1 ELSE 0 END) as frames_won,
                    SUM(CASE WHEN frames.away_player_id IS NOT NULL AND frames.home_score < frames.away_score THEN 1 ELSE 0 END) as frames_lost
                SQL)
                ->groupBy(
                    'frames.home_player_id',
                    'player_users.name',
                    'results.home_team_id',
                    'results.home_team_name',
                );

            $awayAppearances = Frame::query()
                ->join('results', 'results.id', '=', 'frames.result_id')
                ->leftJoin('users as player_users', 'player_users.id', '=', 'frames.away_player_id')
                ->where('results.section_id', $sectionId)
                ->whereNull('results.deleted_at')
                ->where('results.is_confirmed', true)
                ->whereNotNull('frames.away_player_id')
                ->where('frames.away_player_id', '<>', 0)
                ->where(function ($query) {
                    $query->whereNull('frames.home_player_id')
                        ->orWhere('frames.home_player_id', '<>', 0);
                })
                ->selectRaw(<<<'SQL'
                    frames.away_player_id as player_id,
                    player_users.name as player_name,
                    results.away_team_id as team_id,
                    results.away_team_name as team_name,
                    COUNT(*) as frames_played,
                    SUM(CASE WHEN frames.home_player_id IS NOT NULL AND frames.away_score > frames.home_score THEN 1 ELSE 0 END) as frames_won,
                    SUM(CASE WHEN frames.home_player_id IS NOT NULL AND frames.away_score < frames.home_score THEN 1 ELSE 0 END) as frames_lost
                SQL)
                ->groupBy(
                    'frames.away_player_id',
                    'player_users.name',
                    'results.away_team_id',
                    'results.away_team_name',
                );

            $appearanceRows = DB::query()
                ->fromSub($homeAppearances->unionAll($awayAppearances), 'appearances')
                ->selectRaw(<<<'SQL'
                    player_id,
                    player_name,
                    team_id,
                    team_name,
                    SUM(frames_played) as frames_played,
                    SUM(frames_won) as frames_won,
                    SUM(frames_lost) as frames_lost
                SQL)
                ->groupBy('player_id', 'player_name', 'team_id', 'team_name')
                ->get();

            if ($appearanceRows->isEmpty()) {
                return collect();
            }

            $stats = [];

            foreach ($appearanceRows as $row) {
                $playerId = (int) $row->player_id;

                if (! array_key_exists($playerId, $stats)) {
                    $stats[$playerId] = [
                        'name' => $row->player_name ?? 'Unknown',
                        'frames_played' => 0,
                        'frames_won' => 0,
                        'frames_lost' => 0,
                        'teams' => [],
                    ];
                }

                $stats[$playerId]['frames_played'] += (int) $row->frames_played;
                $stats[$playerId]['frames_won'] += (int) $row->frames_won;
                $stats[$playerId]['frames_lost'] += (int) $row->frames_lost;

                if ($row->team_id === null && empty($row->team_name)) {
                    continue;
                }

                $teamKey = $row->team_id !== null
                    ? (string) $row->team_id
                    : sprintf('name:%s', strtolower($row->team_name ?? ''));

                if (! isset($stats[$playerId]['teams'][$teamKey])) {
                    $stats[$playerId]['teams'][$teamKey] = [
                        'id' => $row->team_id,
                        'name' => $row->team_name ?: 'Unknown',
                        'frames' => 0,
                    ];
                }

                $stats[$playerId]['teams'][$teamKey]['frames'] += (int) $row->frames_played;
            }

            $expelledPlayerIds = Expulsion::query()
                ->where('season_id', $this->section->season_id)
                ->where('expellable_type', 'App\\Models\\User')
                ->pluck('expellable_id')
                ->all();

            $playerUsers = User::withTrashed()
                ->select(['id', 'name', 'avatar_path', 'deleted_at'])
                ->whereIn('id', array_keys($stats))
                ->get()
                ->keyBy('id');

            return collect($stats)
                ->reject(fn ($data, $playerId) => in_array((int) $playerId, $expelledPlayerIds, true))
                ->map(function (array $data, $playerId) use ($playerUsers) {
                    $playerId = (int) $playerId;
                    $played = $data['frames_played'] ?? 0;
                    $won = $data['frames_won'] ?? 0;
                    $lost = $data['frames_lost'] ?? 0;
                    $user = $playerUsers->get($playerId);
                    $topTeam = collect($data['teams'] ?? [])
                        ->sortByDesc('frames')
                        ->first();

                    $winPercentage = $played > 0 ? round(($won / $played) * 100, 1) : 0.0;
                    $lossPercentage = $played > 0 ? round(($lost / $played) * 100, 1) : 0.0;

                    return new SectionPlayerAverageData(
                        id: $playerId,
                        name: $user?->name ?? $data['name'] ?? 'Unknown',
                        team_name: $topTeam['name'] ?? null,
                        trashed: $user?->trashed() ?? false,
                        frames_played: $played,
                        frames_won: $won,
                        frames_lost: $lost,
                        frames_won_percentage: $winPercentage,
                        frames_lost_percentage: $lossPercentage,
                        avatar_url: $user ? $user->avatar_url : asset('/images/user.jpg'),
                    );
                })
                ->sort(function (SectionPlayerAverageData $a, SectionPlayerAverageData $b) {
                    if ($a->frames_won !== $b->frames_won) {
                        return $a->frames_won < $b->frames_won ? 1 : -1;
                    }

                    if ($a->frames_lost !== $b->frames_lost) {
                        return $a->frames_lost < $b->frames_lost ? -1 : 1;
                    }

                    return strcmp($a->name, $b->name);
                })
                ->values();
        });
    }

    private function cacheKey(int $sectionId): string
    {
        return sprintf('section:%d:averages', $sectionId);
    }
}
