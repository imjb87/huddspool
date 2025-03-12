<?php

namespace App\Http\Controllers;

use App\Models\Ruleset;
use App\Models\User;

class PlayerController extends Controller
{
    public function index(Ruleset $ruleset)
    {
        $sections = $ruleset->sections()
            ->whereHas('season', function ($query) {
                $query->whereIsOpen(true);
            })->get(); 

        return view('player.index', compact('ruleset', 'sections'));
    }

    public function show(User $player)
    {
        return view('player.show', compact('player'));
    }
}
