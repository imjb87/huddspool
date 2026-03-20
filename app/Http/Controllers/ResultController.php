<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\Result;
use Illuminate\Contracts\View\View;

class ResultController extends Controller
{
    public function show(Result $result): View
    {
        $result->load([
            'fixture' => fn ($query) => $query->with([
                'season',
                'section' => fn ($sectionQuery) => $sectionQuery->withTrashed()->with('ruleset'),
                'venue' => fn ($venueQuery) => $venueQuery->withTrashed(),
            ]),
            'frames.homePlayer',
            'frames.awayPlayer',
            'submittedBy',
        ]);

        return view('result.show', compact('result'));
    }

    public function create(Fixture $fixture)
    {
        $fixture->load([
            'section',
            'venue',
            'homeTeam.players',
            'awayTeam.players',
            'result',
        ]);

        if ($fixture->result && $fixture->result->is_confirmed) {
            return redirect()->route('result.show', $fixture->result);
        }

        $this->authorize('createResult', $fixture);

        return view('result.create', compact('fixture'));
    }
}
