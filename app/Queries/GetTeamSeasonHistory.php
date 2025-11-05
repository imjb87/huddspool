<?php

namespace App\Queries;

use App\Models\Result;
use App\Models\SectionTeam;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class GetTeamSeasonHistory
{
    public function __construct(protected Team $team)
    {
    }

    public function __invoke(): Collection
    {
        $teamId = $this->team->id;

        return Cache::remember("team:season-history:{$teamId}", now()->addMinutes(10), function () use ($teamId) {
            $rows = Result::query()
                ->selectRaw(
                    'seasons.id as season_id, seasons.name as season_name, seasons.slug as season_slug, seasons.dates as season_dates, seasons.is_open,
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
                        + SUM(CASE WHEN results.away_team_id = ? THEN results.away_score ELSE 0 END) as raw_points'
                , [$teamId, $teamId, $teamId, $teamId, $teamId, $teamId])
                ->join('fixtures', 'fixtures.id', '=', 'results.fixture_id')
                ->leftJoin('seasons', 'seasons.id', '=', 'fixtures.season_id')
                ->leftJoin('rulesets', 'rulesets.id', '=', 'fixtures.ruleset_id')
                ->where(function ($query) use ($teamId) {
                    $query->where('results.home_team_id', $teamId)
                        ->orWhere('results.away_team_id', $teamId);
                })
                ->groupBy('seasons.id', 'seasons.name', 'seasons.slug', 'seasons.dates', 'seasons.is_open', 'rulesets.id', 'rulesets.name', 'rulesets.slug')
                ->orderByDesc('seasons.is_open')
                ->orderByDesc('seasons.id')
                ->orderByDesc('rulesets.name')
                ->get();

            $seasonIds = $rows->pluck('season_id')->filter()->unique();

            $deductions = SectionTeam::query()
                ->join('sections', 'sections.id', '=', 'section_team.section_id')
                ->whereIn('sections.season_id', $seasonIds)
                ->where('section_team.team_id', $teamId)
                ->selectRaw('sections.season_id, sections.ruleset_id, SUM(section_team.deducted) as total_deducted')
                ->groupBy('sections.season_id', 'sections.ruleset_id')
                ->get()
                ->mapWithKeys(function ($row) {
                    $key = $row->season_id . ':' . ($row->ruleset_id ?? 'null');
                    return [$key => (int) $row->total_deducted];
                });

            return $rows->map(function ($row) use ($deductions) {
                $key = $row->season_id . ':' . ($row->ruleset_id ?? 'null');
                $deducted = $deductions[$key] ?? 0;

                $wins = (int) $row->wins;
                $draws = (int) $row->draws;
                $losses = (int) $row->losses;
                $played = (int) $row->played;
                $points = (int) $row->raw_points - $deducted;

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
                    'points' => $points,
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
