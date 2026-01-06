<?php

namespace App\Http\Controllers;

use App\Models\Knockout;
use Illuminate\View\View;

class KnockoutController extends Controller
{
    public function index(): View
    {
        $knockouts = Knockout::query()
            ->with('season')
            ->latest('season_id')
            ->get()
            ->groupBy(fn (Knockout $knockout) => $knockout->season->name)
            ->sortKeysDesc();

        return view('knockouts.index', [
            'knockoutGroups' => $knockouts,
        ]);
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
