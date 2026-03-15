<?php

namespace App\Http\Controllers;

use App\Models\Knockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KnockoutController extends Controller
{
    public function index(): RedirectResponse
    {
        return to_route('page.show', 'knockout-dates');
    }

    public function show(Knockout $knockout): View
    {
        $knockout->load([
            'season',
            'rounds.matches.homeParticipant.playerOne',
            'rounds.matches.homeParticipant.playerTwo',
            'rounds.matches.awayParticipant.playerOne',
            'rounds.matches.awayParticipant.playerTwo',
            'rounds.matches.homeParticipant.team',
            'rounds.matches.awayParticipant.team',
            'rounds.matches.forfeitParticipant.playerOne',
            'rounds.matches.forfeitParticipant.playerTwo',
            'rounds.matches.forfeitParticipant.team',
            'rounds.matches.winner',
            'rounds.matches.venue',
            'rounds.matches.previousMatches',
        ]);

        $matchNumbers = [];
        $counter = 1;

        foreach ($knockout->rounds as $round) {
            foreach ($round->matches as $match) {
                $matchNumbers[$match->id] = $counter++;
            }
        }

        return view('knockouts.show', [
            'knockout' => $knockout,
            'matchNumbers' => $matchNumbers,
        ]);
    }
}
