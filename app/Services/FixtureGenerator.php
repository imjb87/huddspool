<?php

namespace App\Services;

use App\Models\Section;

class FixtureGenerator
{
    protected Section $section;

    public function __construct(Section $section)
    {
        $this->section = $section;
    }

    function round_robin($teams)
    {
        $num_teams = count($teams);
        $num_rounds = $num_teams - 1;

        $rounds = array();

        for ($round = 0; $round < $num_rounds; $round++) {
            for ($index = 0; $index < $num_teams / 2; $index++) {
                $local_key = ($index != 0) * ($index - $round) +
                    (($index != 0) && (($index != 0) * ($index - $round) <= 0)) * $num_rounds;

                $away_key = $num_rounds - $index - $round +
                    (($index != 0) && ($num_rounds - $index - $round <= 0)) * $num_rounds;

                // If round is even swap places
                if ($round % 2 == 0) {
                    $rounds[$round][] = array($teams[$local_key], $teams[$away_key]);
                } else {
                    $rounds[$round][] = array($teams[$away_key], $teams[$local_key]);
                }
            }

            $venues = [];
            foreach ($rounds[$round] as $fixture) {
                if (in_array($fixture[0]['venue'], $venues)) {
                    $key = array_search($fixture[0]['venue'], $venues);
                    $rounds[$round][$key] = array_reverse($rounds[$round][$key]);
                } else {
                    $venues[] = $fixture[0]['venue'];
                }
            }
        }

        // Now double the rounds for return leg
        for ($round = 0; $round < $num_rounds; $round++) {
            for ($index = 0; $index < $num_teams / 2; $index++) {
                $local_key = ($index != 0) * ($index - $round) +
                    (($index != 0) && (($index != 0) * ($index - $round) <= 0)) * $num_rounds;

                $away_key = $num_rounds - $index - $round +
                    (($index != 0) && ($num_rounds - $index - $round <= 0)) * $num_rounds;

                $rounds[$round + $num_rounds][] = array($teams[$away_key], $teams[$local_key]);
            }
        }

        return $rounds;
    }

    public function generate()
    {
        $section = $this->section;
        $season = $section->season;
        // get team id and venue id as an array for each team
        $teams = $section->teams->map(function ($team) {
            return [
                'id' => $team->id,
                'venue' => $team->venue_id,
            ];
        })->toArray();
        $fullSchedule = [];

        $schedule = $this->round_robin($teams);

        foreach ($schedule as $week => $fixtures) {
            foreach ($fixtures as $fixture) {

                $home = $section->teams->find($fixture[0]['id']);
                $away = $section->teams->find($fixture[1]['id']);

                $fullSchedule[$week + 1][] = [
                    'week' => $week + 1,
                    'fixture_date' => $season->dates[$week],
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
