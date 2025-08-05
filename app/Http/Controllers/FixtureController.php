<?php

namespace App\Http\Controllers;

use App\Models\Ruleset;
use App\Models\Fixture;
use App\Models\Section;
use App\Queries\GetTeamPlayers;
use Illuminate\Support\Carbon;

class FixtureController extends Controller
{
    public function index(Ruleset $ruleset)
    {
        $sections = $ruleset->sections()
            ->whereHas('season', function ($query) {
                $query->whereIsOpen(true);
            })->get();

        return view('fixture.index', compact('ruleset', 'sections'));
    }

    public function show(Fixture $fixture)
    {
        if ($fixture->home_team_id == 1 || $fixture->away_team_id == 1) {
            abort(404);
        }

        $home_team_players = new GetTeamPlayers($fixture->homeTeam, $fixture->section)();
        $away_team_players = new GetTeamPlayers($fixture->awayTeam, $fixture->section)();

        return view('fixture.show', compact('fixture', 'home_team_players', 'away_team_players'));
    }

    public function download(Section $section)
    {
        $fixtures = $section->fixtures()->get();
        $teams = $section->teams()->get()->sortBy('pivot.sort');
        $dates = $section->season->dates;

        foreach ($dates as $key => $date) {
            $dates[$key] = Carbon::parse($date)->format('d/m');
        }

        $grid = [];

        foreach ($teams as $team) {
            $grid[$team->name] = [];
        }

        // make the grid of fixtures eg. 0v1, 2v3, etc.
        foreach ($fixtures as $fixture) {
            $home_team_id = $fixture->home_team_id;
            $away_team_id = $fixture->away_team_id;

            $homeTeam = $section->teams->find($home_team_id);
            $awayTeam = $section->teams->find($away_team_id);

            if (!isset($grid[$homeTeam->name])) {
                $grid[$homeTeam->name] = [];
            }

            if (!isset($grid[$awayTeam->name])) {
                $grid[$awayTeam->name] = [];
            }

            $homeTeamSort = $homeTeam->pivot->sort == 10 ? 0 : $homeTeam->pivot->sort;
            $awayTeamSort = $awayTeam->pivot->sort == 10 ? 0 : $awayTeam->pivot->sort;

            $grid[$homeTeam->name][$fixture->fixture_date->format('d/m')] = $homeTeamSort . 'v' . $awayTeamSort;
            $grid[$awayTeam->name][$fixture->fixture_date->format('d/m')] = $homeTeamSort . 'v' . $awayTeamSort;
        }        

        return view('fixture.download', compact('grid', 'dates', 'section'));
    }
}
