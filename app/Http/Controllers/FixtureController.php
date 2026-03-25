<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\Ruleset;
use App\Models\Section;
use App\Models\SectionTeam;
use App\Queries\GetTeamPlayers;
use App\Support\FixtureShowPageData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class FixtureController extends Controller
{
    public function index(Request $request, Ruleset $ruleset): RedirectResponse
    {
        $section = $request->filled('section')
            ? $ruleset->openSections()->whereKey($request->integer('section'))->firstOrFail()
            : $ruleset->defaultOpenSection($request->user());

        abort_unless($section, 404);

        $parameters = [
            'ruleset' => $ruleset,
            'section' => $section,
            'tab' => 'fixtures-results',
        ];

        if ($request->filled('week')) {
            $parameters['week'] = $request->integer('week');
        }

        return redirect()->route('ruleset.section.show', $parameters);
    }

    public function show(Fixture $fixture): View
    {
        if ($fixture->isBye()) {
            abort(404);
        }

        $homeTeam = $fixture->homeTeam;
        $awayTeam = $fixture->awayTeam;
        $section = $fixture->section;

        if (! $homeTeam || ! $awayTeam || ! $section) {
            abort(404);
        }

        $home_team_players = new GetTeamPlayers($homeTeam, $section)();
        $away_team_players = new GetTeamPlayers($awayTeam, $section)();
        $pageData = (new FixtureShowPageData)->build($fixture);

        return view('fixture.show', [
            'fixture' => $fixture,
            'home_team_players' => $home_team_players,
            'away_team_players' => $away_team_players,
            'standings' => $pageData->standings,
        ]);
    }

    public function download(Ruleset $ruleset, string $section): Response
    {
        $section = Section::query()
            ->where('ruleset_id', $ruleset->id)
            ->where('slug', $section)
            ->whereHas('season', fn ($query) => $query->where('is_open', true))
            ->firstOrFail();

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

            if (! $homeTeam || ! $awayTeam) {
                continue;
            }

            if (! isset($grid[$homeTeam->name])) {
                $grid[$homeTeam->name] = [];
            }

            if (! isset($grid[$awayTeam->name])) {
                $grid[$awayTeam->name] = [];
            }

            $homeTeamSort = SectionTeam::displaySortValue((int) $homeTeam->pivot->sort);
            $awayTeamSort = SectionTeam::displaySortValue((int) $awayTeam->pivot->sort);

            $grid[$homeTeam->name][$fixture->fixture_date->format('d/m')] = $homeTeamSort.'v'.$awayTeamSort;
            $grid[$awayTeam->name][$fixture->fixture_date->format('d/m')] = $homeTeamSort.'v'.$awayTeamSort;
        }

        $pdf = Pdf::loadView('fixture.print', compact('grid', 'dates', 'section'))
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->stream(sprintf('%s-fixtures.pdf', str_replace(' ', '-', strtolower($section->name))));
    }
}
