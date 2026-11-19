<?php

namespace App\Http\Controllers;

use App\Domain\Results\Rendering\ResultOgImageRenderer;
use App\Models\Fixture;
use App\Models\Result;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class ResultController extends Controller
{
    public function show(Result $result): View
    {
        $result = $this->loadResultForDisplay($result);

        return view('result.show', [
            'result' => $result,
            'submittedAt' => $result->submitted_at ?? $result->created_at,
        ]);
    }

    public function ogImage(Result $result, ResultOgImageRenderer $renderer): Response
    {
        $result = $this->loadResultForDisplay($result);

        return response($renderer->render($result), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function create(Fixture $fixture): RedirectResponse|View
    {
        $fixture->load([
            'section.ruleset',
            'venue',
            'homeTeam.players',
            'awayTeam.players',
            'result',
        ]);

        if ($fixture->result && $fixture->result->is_confirmed) {
            return redirect()->route('result.show', $fixture->result);
        }

        if ($fixture->result) {
            $this->authorize('resumeSubmission', $fixture->result);
        } else {
            $this->authorize('createResult', $fixture);
        }

        return view('result.create', compact('fixture'));
    }

    private function loadResultForDisplay(Result $result): Result
    {
        $result->load([
            'fixture' => fn ($query) => $query->with([
                'season',
                'homeTeam',
                'awayTeam',
                'section' => fn ($sectionQuery) => $sectionQuery->withTrashed()->with('ruleset'),
                'venue' => fn ($venueQuery) => $venueQuery->withTrashed(),
            ]),
            'frames.homePlayer',
            'frames.awayPlayer',
            'submittedBy',
        ]);

        return $result;
    }
}
