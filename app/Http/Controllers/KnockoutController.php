<?php

namespace App\Http\Controllers;

use App\Models\Knockout;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KnockoutController extends Controller
{
    public function index(): RedirectResponse
    {
        return to_route('page.show', 'knockout-dates');
    }

    public function show(string $knockout): View
    {
        $knockout = Knockout::query()
            ->where('slug', $knockout)
            ->whereHas('season', fn ($query) => $query->where('is_open', true))
            ->firstOrFail();

        return view('knockouts.show', [
            'knockout' => $knockout,
        ]);
    }

    public function history(Season $season, string $knockout): View
    {
        abort_if(! $season->hasConcluded(), 404);

        $knockout = Knockout::query()
            ->where('season_id', $season->id)
            ->where('slug', $knockout)
            ->firstOrFail();

        return view('knockouts.show', [
            'knockout' => $knockout,
        ]);
    }
}
