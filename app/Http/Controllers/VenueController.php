<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Venue;
use Illuminate\Contracts\View\View;

class VenueController extends Controller
{
    public function show(Venue $venue): View
    {
        $venueTeams = $venue->teams()
            ->inOpenSeason()
            ->with(['captain', 'openSections.ruleset'])
            ->orderBy('name')
            ->get()
            ->map(fn (Team $team): array => [
                'team' => $team,
                'section_name' => $team->openSection()?->name ?? 'Section TBC',
                'captain_name' => $team->captain?->name,
            ]);

        return view('venue.show', compact('venue', 'venueTeams'));
    }
}
