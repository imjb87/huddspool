<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePlayerAvatarRequest;
use App\Models\Ruleset;
use App\Models\User;
use App\Queries\GetPlayerAverages;
use App\Queries\GetPlayerKnockoutMatches;
use App\Support\KnockoutMatchSummaryRow;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

        $knockoutMatches = new GetPlayerKnockoutMatches($player)();
        $allowKnockoutSubmission = auth()->user()?->isAdmin() ?? false;
        $knockoutRows = $knockoutMatches->map(fn ($match) => KnockoutMatchSummaryRow::forPlayer($match, $player, $allowKnockoutSubmission));

        return view('player.show', compact('player', 'averages', 'knockoutMatches', 'knockoutRows'));
    }

    public function updateAvatar(UpdatePlayerAvatarRequest $request, User $player): RedirectResponse
    {
        $this->authorize('updateAvatar', $player);

        $player->replaceAvatarWithUpload($request->file('avatar'));

        return redirect()
            ->route('player.show', $player)
            ->with('status', 'Avatar updated');
    }
}
