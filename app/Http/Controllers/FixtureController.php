<?php

namespace App\Http\Controllers;

use App\Models\Ruleset;
use App\Models\Fixture;

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
        return view('fixture.show', compact('fixture'));
    }
}
