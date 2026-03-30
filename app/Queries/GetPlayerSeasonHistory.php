<?php

namespace App\Queries;

use App\Models\Expulsion;
use App\Models\Frame;
use App\Models\SectionTeam;
use App\Models\Team;
use App\Models\User;
use App\Support\SeasonLabelFormatter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GetPlayerSeasonHistory
{
    public function __construct(
        protected User $player,
    ) {}

    public function __invoke(): Collection
    {
        $playerId = $this->player->id;

        return Cache::remember("player:season-history:v4:{$playerId}", now()->addMinutes(10), function () use ($playerId) {
            $rows = Frame::query()
                ->selectRaw(
                    'seasons.id as season_id, seasons.name as season_name, seasons.slug as season_slug, seasons.dates as season_dates, seasons.is_open,
                    sections.id as section_id, sections.name as section_name, sections.slug as section_slug,
                    rulesets.id as ruleset_id, rulesets.name as ruleset_name, rulesets.slug as ruleset_slug,
                    CASE
                        WHEN frames.home_player_id = ? THEN results.home_team_id
                        ELSE results.away_team_id
                    END as team_id,
                    CASE
                        WHEN frames.home_player_id = ? THEN results.home_team_name
                        ELSE results.away_team_name
                    END as team_name,
                    COUNT(*) as played,
                    SUM(CASE WHEN (frames.home_player_id = ? AND frames.home_score > frames.away_score)
                        OR (frames.away_player_id = ? AND frames.away_score > frames.home_score)
                    THEN 1 ELSE 0 END) as wins,
                    SUM(CASE WHEN frames.home_score = frames.away_score THEN 1 ELSE 0 END) as draws', [$playerId, $playerId, $playerId, $playerId])
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
                ->whereNotNull('seasons.id')
                ->groupBy(
                    'seasons.id',
                    'seasons.name',
                    'seasons.slug',
                    'seasons.dates',
                    'seasons.is_open',
                    'sections.id',
                    'sections.name',
                    'sections.slug',
                    'rulesets.id',
                    'rulesets.name',
                    'rulesets.slug',
                    'team_id',
                    'team_name',
                )
                ->orderByDesc('seasons.is_open')
                ->orderByDesc('seasons.id')
                ->orderByDesc('sections.name')
                ->orderByDesc('team_name')
                ->selectRaw(
                    'GREATEST(0, COUNT(*) - (
                        SUM(CASE WHEN (frames.home_player_id = ? AND frames.home_score > frames.away_score)
                            OR (frames.away_player_id = ? AND frames.away_score > frames.home_score)
                        THEN 1 ELSE 0 END)
                    ) - (
                        SUM(CASE WHEN frames.home_score = frames.away_score THEN 1 ELSE 0 END)
                    )) as losses',
                    [$playerId, $playerId],
                )
                ->get();

            $sectionTeamPairs = $rows
                ->filter(fn ($row) => $row->section_id && $row->team_id)
                ->map(fn ($row) => $row->section_id.':'.$row->team_id)
                ->unique()
                ->values();

            $deductions = $sectionTeamPairs->isEmpty()
                ? collect()
                : SectionTeam::query()
                    ->where(function ($query) use ($sectionTeamPairs) {
                        foreach ($sectionTeamPairs as $pair) {
                            [$sectionId, $teamId] = explode(':', $pair);
                            $query->orWhere(function ($innerQuery) use ($sectionId, $teamId) {
                                $innerQuery->where('section_id', $sectionId)
                                    ->where('team_id', $teamId);
                            });
                        }
                    })
                    ->selectRaw('section_id, team_id, deducted')
                    ->get()
                    ->mapWithKeys(function ($row) {
                        return [$row->section_id.':'.$row->team_id => (int) $row->deducted];
                    });

            $seasonIds = $rows->pluck('season_id')->filter()->unique();

            $teamExpulsions = Expulsion::query()
                ->whereIn('season_id', $seasonIds)
                ->where('expellable_type', Team::class)
                ->get()
                ->mapWithKeys(fn ($expulsion) => [$expulsion->season_id.':'.$expulsion->expellable_id => true]);

            $playerExpulsions = Expulsion::query()
                ->whereIn('season_id', $seasonIds)
                ->where('expellable_type', User::class)
                ->where('expellable_id', $playerId)
                ->pluck('season_id')
                ->mapWithKeys(fn ($seasonId) => [(int) $seasonId => true]);

            return $rows->map(function ($row) use ($deductions, $teamExpulsions, $playerExpulsions) {
                $teamExpelled = (bool) ($row->season_id && $row->team_id ? ($teamExpulsions[$row->season_id.':'.$row->team_id] ?? false) : false);
                $playerExpelled = (bool) ($playerExpulsions[(int) $row->season_id] ?? false);
                $expelled = $teamExpelled || $playerExpelled;
                $wins = $expelled ? 0 : (int) $row->wins;
                $draws = $expelled ? 0 : (int) $row->draws;
                $played = $expelled ? 0 : (int) $row->played;
                $losses = $expelled ? 0 : (int) $row->losses;
                $winPercentage = $played > 0 ? round(($wins / $played) * 100, 1) : 0.0;
                $lossPercentage = $played > 0 ? round(($losses / $played) * 100, 1) : 0.0;
                $sectionTeamKey = $row->section_id && $row->team_id ? $row->section_id.':'.$row->team_id : null;

                return [
                    'season_id' => $row->season_id,
                    'season_name' => $row->season_name,
                    'season_slug' => $row->season_slug,
                    'season_label' => SeasonLabelFormatter::format($row->season_name, $row->season_dates ?? []),
                    'section_id' => $row->section_id,
                    'section_name' => $row->section_name,
                    'section_slug' => $row->section_slug,
                    'ruleset_id' => $row->ruleset_id,
                    'ruleset_name' => $row->ruleset_name,
                    'ruleset_slug' => $row->ruleset_slug,
                    'team_id' => $row->team_id,
                    'team_name' => $row->team_name,
                    'played' => $played,
                    'wins' => $wins,
                    'draws' => $draws,
                    'losses' => $losses,
                    'win_percentage' => $winPercentage,
                    'loss_percentage' => $lossPercentage,
                    'deducted' => $sectionTeamKey ? ($deductions[$sectionTeamKey] ?? 0) : 0,
                    'team_expelled' => $teamExpelled,
                    'player_expelled' => $playerExpelled,
                    'history_link' => $row->season_slug && $row->ruleset_slug && $row->section_slug
                        ? route('history.section.show', [
                            'season' => $row->season_slug,
                            'ruleset' => $row->ruleset_slug,
                            'section' => $row->section_slug,
                        ])
                        : null,
                ];
            });
        });
    }
}
