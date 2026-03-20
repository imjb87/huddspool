<?php

namespace App\Queries;

use App\Models\Frame;
use App\Models\User;
use App\Support\SeasonLabelFormatter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GetPlayerSeasonHistory
{
    public function __construct(
        protected User $player,
        protected ?SeasonLabelFormatter $seasonLabelFormatter = null,
    ) {}

    public function __invoke(): Collection
    {
        $playerId = $this->player->id;

        return Cache::remember("player:season-history:v2:{$playerId}", now()->addMinutes(10), function () use ($playerId) {
            $rows = Frame::query()
                ->selectRaw(
                    'seasons.id as season_id, seasons.name as season_name, seasons.slug as season_slug, seasons.dates as season_dates, seasons.is_open,
                    sections.id as section_id, sections.name as section_name,
                    rulesets.id as ruleset_id, rulesets.name as ruleset_name, rulesets.slug as ruleset_slug,
                    CASE
                        WHEN frames.home_player_id = ? THEN results.home_team_name
                        ELSE results.away_team_name
                    END as team_name,
                    COUNT(*) as played,
                    SUM(CASE WHEN (frames.home_player_id = ? AND frames.home_score > frames.away_score)
                        OR (frames.away_player_id = ? AND frames.away_score > frames.home_score)
                    THEN 1 ELSE 0 END) as wins,
                    SUM(CASE WHEN frames.home_score = frames.away_score THEN 1 ELSE 0 END) as draws', [$playerId, $playerId, $playerId])
                ->join('results', 'results.id', '=', 'frames.result_id')
                ->join('fixtures', 'fixtures.id', '=', 'results.fixture_id')
                ->leftJoin('seasons', 'seasons.id', '=', 'fixtures.season_id')
                ->leftJoin('sections', 'sections.id', '=', 'fixtures.section_id')
                ->leftJoin('rulesets', 'rulesets.id', '=', 'fixtures.ruleset_id')
                ->where(function ($query) use ($playerId) {
                    $query->where('frames.home_player_id', $playerId)
                        ->orWhere('frames.away_player_id', $playerId);
                })
                ->where(function ($query) {
                    $query->where('seasons.is_open', false)
                        ->orWhereNull('seasons.is_open');
                })
                ->groupBy(
                    'seasons.id',
                    'seasons.name',
                    'seasons.slug',
                    'seasons.dates',
                    'seasons.is_open',
                    'sections.id',
                    'sections.name',
                    'rulesets.id',
                    'rulesets.name',
                    'rulesets.slug',
                    'team_name',
                )
                ->orderByDesc('seasons.is_open')
                ->orderByDesc('seasons.id')
                ->orderByDesc('sections.name')
                ->orderByDesc('team_name')
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
                    'season_label' => $this->seasonLabelFormatter()->for($row->season_name, $row->season_dates ?? []),
                    'section_id' => $row->section_id,
                    'section_name' => $row->section_name,
                    'ruleset_id' => $row->ruleset_id,
                    'ruleset_name' => $row->ruleset_name,
                    'ruleset_slug' => $row->ruleset_slug,
                    'team_name' => $row->team_name,
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

    protected function seasonLabelFormatter(): SeasonLabelFormatter
    {
        return $this->seasonLabelFormatter ??= app(SeasonLabelFormatter::class);
    }
}
