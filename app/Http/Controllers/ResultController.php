<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\Result;

class ResultController extends Controller
{
    public function show(Result $result)
    {
        $result->load([
            'fixture.section.ruleset',
            'fixture.venue',
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
