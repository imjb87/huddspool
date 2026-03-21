<?php

namespace App\Http\Controllers;

use App\Models\Season;
use App\Models\SeasonEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;

class SeasonEntryController extends Controller
{
    public function show(Season $season): View
    {
        return view('season-entry.show', [
            'season' => $season,
        ]);
    }

    public function confirmation(Season $season, SeasonEntry $entry): View
    {
        abort_unless($entry->season_id === $season->id, 404);

        return view('season-entry.confirmation', [
            'season' => $season,
            'entry' => $this->loadEntry($entry),
        ]);
    }

    public function invoice(Season $season, SeasonEntry $entry): Response
    {
        abort_unless($entry->season_id === $season->id, 404);

        $entry = $this->loadEntry($entry);

        return Pdf::loadView('season-entry.invoice', [
            'season' => $season,
            'entry' => $entry,
        ])->stream(sprintf('%s-invoice.pdf', $entry->reference));
    }

    private function loadEntry(SeasonEntry $entry): SeasonEntry
    {
        return $entry->load([
            'existingVenue',
            'teams.ruleset',
            'teams.secondRuleset',
            'knockoutRegistrations.knockout',
        ]);
    }
}
