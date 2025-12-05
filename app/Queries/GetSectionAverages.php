<?php

namespace App\Queries;

use App\Data\SectionPlayerAverageData;
use App\Models\Expulsion;
use App\Models\Frame;
use App\Models\Section;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GetSectionAverages
{
    public function __construct(
        protected Section $section,
        protected int $page = 1,
        protected int $perPage = 10
    ) {
    }

    /**
     * @return Collection<int, SectionPlayerAverageData>
     */
    public function __invoke(): Collection
    {
        $sectionId = $this->section->id;
        $cacheKey = sprintf('section:%d:averages', $sectionId);

        $allPlayers = Cache::remember($cacheKey, now()->addMinutes(2), function () use ($sectionId) {
            $frames = Frame::query()
                ->with([
                    'homePlayer:id,name',
                    'awayPlayer:id,name',
                    'result:id,section_id,home_team_id,home_team_name,away_team_id,away_team_name',
                ])
                ->whereHas('result', fn ($query) => $query->where('section_id', $sectionId))
                ->get();

            if ($frames->isEmpty()) {
                return collect();
            }

            $stats = [];

            $ensurePlayer = function (int $playerId, ?string $name) use (&$stats) {
                if (! array_key_exists($playerId, $stats)) {
                    $stats[$playerId] = [
                        'name' => $name ?? 'Unknown',
                        'frames_played' => 0,
                        'frames_won' => 0,
                        'frames_lost' => 0,
                        'teams' => [],
                    ];
                }
            };

            $trackTeam = function (int $playerId, ?int $teamId, ?string $teamName) use (&$stats): void {
                if ($teamId === null && empty($teamName)) {
                    return;
                }

                $key = $teamId !== null ? (string) $teamId : sprintf('name:%s', strtolower($teamName ?? ''));

                if (! isset($stats[$playerId]['teams'][$key])) {
                    $stats[$playerId]['teams'][$key] = [
                        'id' => $teamId,
                        'name' => $teamName ?: 'Unknown',
                        'frames' => 0,
                    ];
                }

                $stats[$playerId]['teams'][$key]['frames']++;
            };

            foreach ($frames as $frame) {
                $homeId = $frame->home_player_id;
                $awayId = $frame->away_player_id;

                if ($homeId !== null) {
                    $ensurePlayer($homeId, $frame->homePlayer?->name);
                    $trackTeam(
                        $homeId,
                        $frame->result?->home_team_id,
                        $frame->result?->home_team_name
                    );
                    $stats[$homeId]['frames_played']++;
                }

                if ($awayId !== null) {
                    $ensurePlayer($awayId, $frame->awayPlayer?->name);
                    $trackTeam(
                        $awayId,
                        $frame->result?->away_team_id,
                        $frame->result?->away_team_name
                    );
                    $stats[$awayId]['frames_played']++;
                }

                if ($homeId !== null && $awayId !== null) {
                    if ($frame->home_score > $frame->away_score) {
                        $stats[$homeId]['frames_won']++;
                        $stats[$awayId]['frames_lost']++;
                    } elseif ($frame->home_score < $frame->away_score) {
                        $stats[$awayId]['frames_won']++;
                        $stats[$homeId]['frames_lost']++;
                    }
                }
            }

            $expelledPlayerIds = Expulsion::query()
                ->where('season_id', $this->section->season_id)
                ->where('expellable_type', 'App\\Models\\User')
                ->pluck('expellable_id')
                ->all();

            $playerUsers = User::withTrashed()
                ->select(['id', 'name', 'avatar_path'])
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

        $offset = max(0, ($this->page - 1) * $this->perPage);

        return $allPlayers
            ->slice($offset, $this->perPage)
            ->values();
    }
}
