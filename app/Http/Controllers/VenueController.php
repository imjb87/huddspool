<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Contracts\View\View;

class VenueController extends Controller
{
    public function show(Venue $venue): View
    {
        $teams = $venue->teams()
            ->inOpenSeason()
            ->with(['captain', 'openSections.ruleset'])
            ->orderBy('name')
            ->get();

        return view('venue.show', compact('venue', 'teams'));
    }
}
