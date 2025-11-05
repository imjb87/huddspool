<?php

namespace App\Queries;

use App\Models\Frame;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class GetPlayerSeasonHistory
{
    public function __construct(protected User $player)
    {
    }

    public function __invoke(): Collection
    {
        $playerId = $this->player->id;

        return Cache::remember("player:season-history:{$playerId}", now()->addMinutes(10), function () use ($playerId) {
            $rows = Frame::query()
                ->selectRaw(
                    'seasons.id as season_id, seasons.name as season_name, seasons.slug as season_slug, seasons.dates as season_dates, seasons.is_open,
                    rulesets.id as ruleset_id, rulesets.name as ruleset_name, rulesets.slug as ruleset_slug,
                    COUNT(*) as played,
                    SUM(CASE WHEN (frames.home_player_id = ? AND frames.home_score > frames.away_score)
                        OR (frames.away_player_id = ? AND frames.away_score > frames.home_score)
                    THEN 1 ELSE 0 END) as wins,
                    SUM(CASE WHEN frames.home_score = frames.away_score THEN 1 ELSE 0 END) as draws'
                , [$playerId, $playerId])
                ->join('results', 'results.id', '=', 'frames.result_id')
                ->join('fixtures', 'fixtures.id', '=', 'results.fixture_id')
                ->leftJoin('seasons', 'seasons.id', '=', 'fixtures.season_id')
                ->leftJoin('rulesets', 'rulesets.id', '=', 'fixtures.ruleset_id')
                ->where(function ($query) use ($playerId) {
                    $query->where('frames.home_player_id', $playerId)
                        ->orWhere('frames.away_player_id', $playerId);
                })
                ->groupBy('seasons.id', 'seasons.name', 'seasons.slug', 'seasons.dates', 'seasons.is_open', 'rulesets.id', 'rulesets.name', 'rulesets.slug')
                ->orderByDesc('seasons.is_open')
                ->orderByDesc('seasons.id')
                ->orderByDesc('rulesets.name')
                ->get();

            return $rows->map(function ($row) {
                $wins = (int) $row->wins;
                $draws = (int) $row->draws;
                $played = (int) $row->played;
                $losses = max(0, $played - $wins - $draws);
                $winPercentage = $played > 0 ? round(($wins / $played) * 100, 1) : 0.0;
                $lossPercentage = $played > 0 ? round(($losses / $played) * 100, 1) : 0.0;

                return [
                    'season_id' => $row->season_id,
                    'season_name' => $row->season_name,
                    'season_slug' => $row->season_slug,
                    'season_label' => $this->determineSeasonLabel($row->season_name, $row->season_dates ?? []),
                    'ruleset_id' => $row->ruleset_id,
                    'ruleset_name' => $row->ruleset_name,
                    'ruleset_slug' => $row->ruleset_slug,
                    'played' => $played,
                    'wins' => $wins,
                    'draws' => $draws,
                    'losses' => $losses,
                    'win_percentage' => $winPercentage,
                    'loss_percentage' => $lossPercentage,
                ];
            })->filter(fn ($entry) => $entry['season_id']);
        });
    }

    protected function determineSeasonLabel(?string $seasonName, mixed $seasonDates): string
    {
        $dates = collect(is_string($seasonDates) ? json_decode($seasonDates, true) : $seasonDates);

        $firstDate = $dates
            ->flatten()
            ->filter()
            ->map(function ($value) {
                if ($value instanceof Carbon) {
                    return $value;
                }

                if (is_string($value)) {
                    try {
                        return Carbon::parse($value);
                    } catch (\Throwable) {
                        return null;
                    }
                }

                return null;
            })
            ->filter()
            ->sort()
            ->first();

        if ($firstDate) {
            return $firstDate->isoFormat('MMM YY');
        }

        if ($seasonName) {
            if (preg_match('/\d{4}/', $seasonName, $match)) {
                return Carbon::createFromDate((int) $match[0], 1, 1)->isoFormat('MMM YY');
            }

            if (preg_match('/\d{2}/', $seasonName, $match)) {
                $year = (int) $match[0];
                $year += $year >= 70 ? 1900 : 2000;

                return Carbon::createFromDate($year, 1, 1)->isoFormat('MMM YY');
            }

            return $seasonName;
        }

        return 'Unknown';
    }
}
