<?php

namespace App\Services;

use App\Models\Section;

class FixtureService
{
    protected Section $section;

    public function __construct(Section $section)
    {
        $this->section = $section;
    }

    function createSchedule($teams)
    {
        $weeks = [
            [[0,1],[9,2],[8,3],[7,4],[6,5]],
            [[3,1],[2,0],[4,9],[5,8],[6,7]],
            [[1,5],[2,4],[0,3],[9,6],[8,7]],
            [[7,1],[6,2],[5,3],[4,0],[8,9]],
            [[1,9],[2,8],[3,7],[4,6],[0,5]],
            [[1,2],[9,3],[8,4],[7,5],[0,6]],
            [[4,1],[3,2],[5,9],[6,8],[7,0]],
            [[1,6],[2,5],[3,4],[9,7],[0,8]],
            [[8,1],[7,2],[6,3],[5,4],[9,0]],
            [[1,0],[2,9],[3,8],[4,7],[5,6]],
            [[1,3],[0,2],[9,4],[8,5],[7,6]],
            [[5,1],[4,2],[3,0],[6,9],[7,8]],
            [[1,7],[2,6],[3,5],[0,4],[9,8]],
            [[9,1],[8,2],[7,3],[6,4],[5,0]],
            [[2,1],[3,9],[4,8],[5,7],[6,0]],
            [[1,4],[2,3],[9,5],[8,6],[0,7]],
            [[6,1],[5,2],[4,3],[7,9],[8,0]],
            [[1,8],[2,7],[3,6],[4,5],[0,9]]
        ];
    
        $schedule = [];

        foreach ($weeks as $week => $fixtures) {
            foreach ($fixtures as $fixture) {
                $schedule[$week][] = [
                    $teams[$fixture[0]],
                    $teams[$fixture[1]],
                ];
            }
        }

        return $schedule;
    }

    public function generate()
    {
        // get team id and venue id as an array for each team order by "sort"
        $teams = $this->section->teams
                    ->sortBy(fn($team) => $team->pivot->sort)
                    ->pluck('id')
                    ->toArray();

        // pop the last team from the array and add it to the beginning
        array_unshift($teams, array_pop($teams));
        $fullSchedule = [];

        // generate schedule
        $schedule = $this->createSchedule($teams);

        $dates = $this->section->season?->dates ?? [];

        foreach ($schedule as $week => $fixtures) {
            foreach ($fixtures as $fixture) {

                $home = $this->section->teams->find($fixture[0]);
                $away = $this->section->teams->find($fixture[1]);
                if (! $home || ! $away) {
                    continue;
                }

                $fullSchedule[$week + 1][] = [
                    'week' => $week + 1,
                    'fixture_date' => $dates[$week] ?? null,
                    'home_team_id' => $home->id,
                    'away_team_id' => $away->id,
                    'season_id' => $this->section->season->id,
                    'section_id' => $this->section->id,
                    'venue_id' => $home->venue_id ?? null,
                    'ruleset_id' => $this->section->ruleset_id,
                ];
            }
        }

        return $fullSchedule;
    }
}
