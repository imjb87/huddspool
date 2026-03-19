<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePlayerAvatarRequest;
use App\KnockoutType;
use App\Models\KnockoutMatch;
use App\Models\Ruleset;
use App\Models\User;
use App\Queries\GetPlayerAverages;
use App\Queries\GetPlayerFrames;
use App\Queries\GetPlayerSeasonHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlayerController extends Controller
{
    public function index(Request $request, Ruleset $ruleset): RedirectResponse
    {
        $section = $request->filled('section')
            ? $ruleset->openSections()->whereKey($request->integer('section'))->firstOrFail()
            : $ruleset->defaultOpenSection($request->user());

        abort_unless($section, 404);

        $parameters = [
            'ruleset' => $ruleset,
            'section' => $section,
            'tab' => 'averages',
        ];

        if ($request->filled('page')) {
            $parameters['page'] = $request->integer('page');
        }

        return redirect()->route('ruleset.section.show', $parameters);
    }

    public function show(User $player)
    {
        $section = $player->team?->openSection();
        $averages = $player->team ? (new GetPlayerAverages($player, $section))() : null;
        $frames = $player->team ? (new GetPlayerFrames($player, $section))() : null;
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
            ->whereHas('round', fn ($query) => $query->where('is_visible', true))
            ->whereHas('round.knockout', fn ($query) => $query->whereIn('type', [
                KnockoutType::Singles->value,
                KnockoutType::Doubles->value,
            ]))
            ->where(function ($query) use ($participantQuery) {
                $query->whereHas('homeParticipant', $participantQuery)
                    ->orWhereHas('awayParticipant', $participantQuery);
            })
            ->orderByDesc('starts_at')
            ->orderByDesc('id')
            ->get();

        return view('player.show', compact('player', 'averages', 'frames', 'history', 'knockoutMatches'));
    }

    public function updateAvatar(UpdatePlayerAvatarRequest $request, User $player)
    {
        $this->authorize('updateAvatar', $player);

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
