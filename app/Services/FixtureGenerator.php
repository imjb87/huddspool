<?php

namespace App\Services;

use App\Models\Section;
use Laravel\RoundRobin\RoundRobin;

class FixtureGenerator
{
    protected Section $section;

    public function __construct(Section $section)
    {
        $this->section = $section;
    }

    public function generate()
    {
        $section = $this->section;
        $season = $section->season;
        $teams = $section->teams->pluck('id')->toArray();
        $fullSchedule = [];

        $roundrobin = new RoundRobin($teams);
        $roundrobin->doNotShuffle();
        $roundrobin->doubleRoundRobin();
        $schedule = $roundrobin->build();

        foreach ($schedule as $week => $fixtures) {
            foreach ($fixtures as $fixture) {

                $home = $section->teams->find($fixture[0]);
                $away = $section->teams->find($fixture[1]);

                $fullSchedule[$week][] = [
                    'week' => $week,
                    'fixture_date' => $season->dates[$week-1],
                    'home_team_id' => $home->id,
                    'home_team_name' => $home->name,
                    'away_team_id' => $away->id,
                    'away_team_name' => $away->name,
                    'season_id' => $season->id,
                    'section_id' => $section->id,
                    'venue_id' => $home->venue_id ?? null,
                    'venue_name' => $home->venue->name ?? null,
                    'ruleset_id' => $section->ruleset_id,
                ];
            }
        }

        return $fullSchedule;
    }
       
}
