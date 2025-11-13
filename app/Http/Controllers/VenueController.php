<?php

namespace App\Http\Controllers;

use App\Models\Venue;

class VenueController extends Controller
{
    public function show(Venue $venue)
    {
        $teams = $venue->teams()
            ->inOpenSeason()
            ->with('captain')
            ->orderBy('name')
            ->get();

        return view('venue.show', compact('venue', 'teams'));
    }
}
