<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\Result;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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

    public function ogImage(Result $result): Response
    {
        $result = $this->loadResultForDisplay($result);
        $fixture = $result->fixture;
        $section = $fixture->section;
        $season = $fixture->season;
        $ruleset = $section?->ruleset;
        $submittedAt = $result->submitted_at ?? $result->created_at;
        $homeScore = (int) $result->home_score;
        $awayScore = (int) $result->away_score;
        $homeFill = $homeScore === $awayScore ? '#f3f4f6' : ($homeScore > $awayScore ? '#14532d' : '#7f1d1d');
        $homeText = $homeScore === $awayScore ? '#374151' : '#ffffff';
        $awayFill = $homeScore === $awayScore ? '#f3f4f6' : ($awayScore > $homeScore ? '#14532d' : '#7f1d1d');
        $awayText = $homeScore === $awayScore ? '#374151' : '#ffffff';
        $outcomeLabel = $homeScore === $awayScore
            ? 'Draw'
            : ($homeScore > $awayScore ? $result->home_team_name.' win' : $result->away_team_name.' win');

        $svg = view('result.og-image', [
            'result' => $result,
            'fixture' => $fixture,
            'section' => $section,
            'season' => $season,
            'ruleset' => $ruleset,
            'submittedAt' => $submittedAt,
            'outcomeLabel' => $this->truncateOgText($outcomeLabel, 22),
            'homeFill' => $homeFill,
            'homeText' => $homeText,
            'awayFill' => $awayFill,
            'awayText' => $awayText,
            'homeTeamDisplay' => $this->truncateOgText($result->home_team_name, 24),
            'awayTeamDisplay' => $this->truncateOgText($result->away_team_name, 24),
            'rulesetDisplay' => $this->truncateOgText($ruleset?->name ?? 'Ruleset unavailable', 28),
            'seasonDisplay' => $this->truncateOgText($season?->name ?? 'League season', 24),
            'submittedByDisplay' => $result->is_confirmed && $result->submittedBy
                ? $this->truncateOgText('Submitted by '.$result->submittedBy->name.' on '.$submittedAt->format('j M Y'), 42)
                : null,
        ])->render();

        return response($this->renderOgImagePng($svg), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=3600',
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

    private function renderOgImagePng(string $svg): string
    {
        $svgPath = tempnam(sys_get_temp_dir(), 'result-og-svg-');
        $pngPath = tempnam(sys_get_temp_dir(), 'result-og-png-');

        try {
            if ($svgPath === false || $pngPath === false) {
                abort(500, 'Unable to prepare result OG image rendering.');
            }

            file_put_contents($svgPath, $svg);

            $process = new Process([
                'rsvg-convert',
                '--format=png',
                '--width=1200',
                '--height=630',
                '--output='.$pngPath,
                $svgPath,
            ]);

            $process->mustRun();

            $png = file_get_contents($pngPath);

            if ($png === false) {
                abort(500, 'Unable to read rendered result OG image.');
            }

            return $png;
        } catch (ProcessFailedException) {
            abort(500, 'Unable to render result OG image.');
        } finally {
            if ($svgPath !== false && file_exists($svgPath)) {
                unlink($svgPath);
            }

            if ($pngPath !== false && file_exists($pngPath)) {
                unlink($pngPath);
            }
        }
    }

    private function truncateOgText(string $value, int $maxLength): string
    {
        $value = trim($value);

        if (mb_strlen($value) <= $maxLength) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $maxLength - 1)).'…';
    }
}
