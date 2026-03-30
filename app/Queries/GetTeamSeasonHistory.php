<?php

namespace App\Queries;

use App\Models\Expulsion;
use App\Models\Result;
use App\Models\SectionTeam;
use App\Models\Team;
use App\Support\SeasonLabelFormatter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GetTeamSeasonHistory
{
    public function __construct(
        protected Team $team,
    ) {}

    public function __invoke(): Collection
    {
        $teamId = $this->team->id;

        return Cache::remember("team:season-history:v2:{$teamId}", now()->addMinutes(10), function () use ($teamId) {
            $rows = Result::query()
                ->selectRaw(
                    'seasons.id as season_id, seasons.name as season_name, seasons.slug as season_slug, seasons.dates as season_dates, seasons.is_open,
                    sections.id as section_id, sections.name as section_name, sections.slug as section_slug,
                    rulesets.id as ruleset_id, rulesets.name as ruleset_name, rulesets.slug as ruleset_slug,
                    COUNT(*) as played,
                    SUM(CASE WHEN (results.home_team_id = ? AND results.home_score > results.away_score)
                        OR (results.away_team_id = ? AND results.away_score > results.home_score)
                    THEN 1 ELSE 0 END) as wins,
                    SUM(CASE WHEN results.home_score = results.away_score THEN 1 ELSE 0 END) as draws,
                    SUM(CASE WHEN (results.home_team_id = ? AND results.home_score < results.away_score)
                        OR (results.away_team_id = ? AND results.away_score < results.home_score)
                    THEN 1 ELSE 0 END) as losses,
                    SUM(CASE WHEN results.home_team_id = ? THEN results.home_score ELSE 0 END)
                        + SUM(CASE WHEN results.away_team_id = ? THEN results.away_score ELSE 0 END) as raw_points', [$teamId, $teamId, $teamId, $teamId, $teamId, $teamId])
                ->join('fixtures', 'fixtures.id', '=', 'results.fixture_id')
                ->leftJoin('seasons', 'seasons.id', '=', 'fixtures.season_id')
                ->leftJoin('sections', 'sections.id', '=', 'fixtures.section_id')
                ->leftJoin('rulesets', 'rulesets.id', '=', 'fixtures.ruleset_id')
                ->where(function ($query) use ($teamId) {
                    $query->where('results.home_team_id', $teamId)
                        ->orWhere('results.away_team_id', $teamId);
                })
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
                    'rulesets.slug'
                )
                ->orderByDesc('seasons.is_open')
                ->orderByDesc('seasons.id')
                ->orderByDesc('rulesets.name')
                ->get();

            $seasonIds = $rows->pluck('season_id')->filter()->unique();

            $sectionIds = $rows->pluck('section_id')->filter()->unique();

            $deductions = SectionTeam::query()
                ->whereIn('section_id', $sectionIds)
                ->where('team_id', $teamId)
                ->selectRaw('section_id, deducted')
                ->get()
                ->mapWithKeys(function ($row) {
                    return [(int) $row->section_id => (int) $row->deducted];
                });

            $teamExpulsions = Expulsion::query()
                ->whereIn('season_id', $seasonIds)
                ->where('expellable_type', Team::class)
                ->where('expellable_id', $teamId)
                ->pluck('season_id')
                ->mapWithKeys(fn ($seasonId) => [(int) $seasonId => true]);

            return $rows->map(function ($row) use ($deductions, $teamExpulsions) {
                $deducted = $deductions[(int) $row->section_id] ?? 0;
                $teamExpelled = (bool) ($teamExpulsions[(int) $row->season_id] ?? false);

                $wins = $teamExpelled ? 0 : (int) $row->wins;
                $draws = $teamExpelled ? 0 : (int) $row->draws;
                $losses = $teamExpelled ? 0 : (int) $row->losses;
                $played = $teamExpelled ? 0 : (int) $row->played;
                $points = $teamExpelled ? 0 : (int) $row->raw_points - $deducted;

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
                    'played' => $played,
                    'wins' => $wins,
                    'draws' => $draws,
                    'losses' => $losses,
                    'points' => $points,
                    'deducted' => $deducted,
                    'team_expelled' => $teamExpelled,
                ];
            })->filter(fn ($entry) => $entry['season_id']);
        });
    }
}
