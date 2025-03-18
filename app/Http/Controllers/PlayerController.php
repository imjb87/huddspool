<?php

namespace App\Http\Controllers;

use App\Models\Ruleset;
use App\Models\User;
use App\Queries\GetPlayerAverages;
use App\Queries\GetPlayerFrames;

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
        $averages = $player->team ? (new GetPlayerAverages($player, $player->team?->section()))() : null;
        $frames = $player->team ? (new GetPlayerFrames($player, $player->team?->section()))() : null;

        return view('player.show', compact('player', 'averages', 'frames'));
    }
}
