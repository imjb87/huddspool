<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePlayerAvatarRequest;
use App\Models\Ruleset;
use App\Models\User;
use App\Queries\GetPlayerAverages;
use App\Queries\GetPlayerFrames;
use App\Queries\GetPlayerKnockoutMatches;
use App\Queries\GetPlayerSeasonHistory;
use App\Support\FrameSummaryRow;
use App\Support\KnockoutMatchSummaryRow;
use Illuminate\Contracts\View\View;
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

    public function show(User $player): View
    {
        $section = $player->team?->openSection();
        $averages = $player->team ? (new GetPlayerAverages($player, $section))() : null;
        $frames = $player->team ? (new GetPlayerFrames($player, $section))() : null;
        $history = (new GetPlayerSeasonHistory($player))();

        $knockoutMatches = new GetPlayerKnockoutMatches($player)();
        $frameRows = $frames?->map(fn ($frame) => FrameSummaryRow::fromFrame($frame, $player->id));
        $knockoutRows = $knockoutMatches->map(fn ($match) => KnockoutMatchSummaryRow::neutral($match));

        return view('player.show', compact('player', 'averages', 'frames', 'frameRows', 'history', 'knockoutMatches', 'knockoutRows'));
    }

    public function updateAvatar(UpdatePlayerAvatarRequest $request, User $player): RedirectResponse
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
