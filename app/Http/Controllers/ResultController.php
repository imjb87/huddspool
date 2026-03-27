<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\Result;
use App\Support\ResultShareImageGenerator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ResultController extends Controller
{
    public function show(Result $result, ResultShareImageGenerator $shareImages): View
    {
        $result = $this->loadResultRelations($result);

        return view('result.show', [
            'result' => $result,
            'submittedAt' => $result->submitted_at ?? $result->created_at,
            'shareTitle' => $this->shareTitle($result),
            'shareDescription' => $this->shareDescription($result),
            'shareImageUrl' => $shareImages->url($result),
        ]);
    }

    public function shareImage(Result $result, ResultShareImageGenerator $shareImages, ?string $version = null): Response
    {
        $result = $this->loadResultRelations($result);

        $requestedVersion = blank($version) ? null : $version;
        $path = $shareImages->ensureGenerated($result, $requestedVersion);
        $image = Storage::disk('local')->get($path);

        $currentVersion = $shareImages->version($result);
        $isCurrentVersion = $requestedVersion !== null && $requestedVersion === $currentVersion;

        return response($image, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => $isCurrentVersion
                ? 'public, max-age=31536000, immutable'
                : 'public, max-age=300',
            'Last-Modified' => ($result->updated_at ?? $result->created_at)?->toRfc7231String() ?? now()->toRfc7231String(),
        ]);
    }

    public function create(Fixture $fixture): RedirectResponse|View
    {
        $fixture->load([
            'section',
            'venue',
            'homeTeam.players',
            'awayTeam.players',
            'result',
        ]);

        if ($fixture->result && $fixture->result->is_confirmed) {
            return redirect()->route('result.show', $fixture->result);
        }

        $this->authorize('createResult', $fixture);

        return view('result.create', compact('fixture'));
    }

    private function loadResultRelations(Result $result): Result
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

    private function shareTitle(Result $result): string
    {
        return sprintf(
            '%s %s-%s %s',
            $result->home_team_name,
            $result->home_score ?? '–',
            $result->away_score ?? '–',
            $result->away_team_name,
        );
    }

    private function shareDescription(Result $result): string
    {
        $fixture = $result->fixture;
        $parts = array_filter([
            $fixture?->section?->name,
            $fixture?->section?->ruleset?->name,
            $fixture?->fixture_date?->format('j M Y'),
            $fixture?->venue?->name,
        ]);

        return implode(' • ', $parts);
    }
}
