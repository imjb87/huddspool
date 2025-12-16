<?php

namespace App\Http\Controllers;

use App\Models\KnockoutMatch;
use App\Models\Ruleset;
use App\Models\User;
use App\Queries\GetPlayerAverages;
use App\Queries\GetPlayerFrames;
use App\Queries\GetPlayerSeasonHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $history = (new GetPlayerSeasonHistory($player))();

        $participantQuery = function ($query) use ($player) {
            $query->where('player_one_id', $player->id)
                ->orWhere('player_two_id', $player->id);

            if ($player->team_id) {
                $query->orWhere('team_id', $player->team_id);
            }
        };

        $knockoutMatches = KnockoutMatch::query()
            ->with([
                'round.knockout',
                'homeParticipant',
                'awayParticipant',
                'venue',
                'forfeitParticipant',
            ])
            ->whereNull('winner_participant_id')
            ->where(function ($query) use ($participantQuery) {
                $query->whereHas('homeParticipant', $participantQuery)
                    ->orWhereHas('awayParticipant', $participantQuery);
            })
            ->orderBy('starts_at')
            ->orderBy('id')
            ->get()
            ->filter(fn (KnockoutMatch $match) => $match->userCanSubmit($player))
            ->values();

        return view('player.show', compact('player', 'averages', 'frames', 'history', 'knockoutMatches'));
    }

    public function updateAvatar(Request $request, User $player)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:5120'], // allow images up to 5MB
        ]);

        $user = $request->user();

        abort_unless(
            $user && ($user->is($player) || $user->isAdmin()),
            403
        );

        $newPath = $request->file('avatar')->store('avatars', 'public');

        if ($player->avatar_path) {
            Storage::disk('public')->delete($player->avatar_path);
        }

        $player->update([
            'avatar_path' => $newPath,
        ]);

        return redirect()
            ->route('player.show', $player)
            ->with('status', 'Avatar updated');
    }
}
