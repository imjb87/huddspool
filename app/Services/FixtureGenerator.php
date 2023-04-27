<?php

namespace App\Services;

use App\Models\Section;
use App\Services\Fixture;

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

        $fix = new Fixture($teams);

        $schedule = $fix->getSchedule();

        foreach ($schedule as $week => $fixtures) {
            foreach ($fixtures as $fixture) {

                $home = $section->teams->find($fixture[0]);
                $away = $section->teams->find($fixture[1]);

                $fullSchedule[$week + 1][] = [
                    'week' => $week + 1,
                    'fixture_date' => $season->dates[$week],
                    'home_team_id' => $home->id,
                    'home_team_name' => $home->name,
                    'away_team_id' => $away->id,
                    'away_team_name' => $away->name,
                    'section_id' => $section->id,
                    'venue_id' => $home->venue_id,
                    'venue_name' => $home->venue->name,
                ];
            }
        }

        return $fullSchedule;
    }
       
}
