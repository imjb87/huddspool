<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\Result;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Throwable;

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

        $card = [
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
        ];

        $svg = view('result.og-image', $card)->render();

        return response($this->renderOgImagePng($svg, $card), 200, [
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

    /**
     * @param  array<string, mixed>  $card
     */
    private function renderOgImagePng(string $svg, array $card): string
    {
        try {
            return $this->renderOgImagePngWithRsvg($svg);
        } catch (Throwable) {
            if (function_exists('imagecreatetruecolor')) {
                return $this->renderOgImagePngWithGd($card);
            }

            abort(500, 'Unable to render result OG image.');
        }
    }

    private function renderOgImagePngWithRsvg(string $svg): string
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
            throw $this->withGenericOgImageMessage();
        } finally {
            if ($svgPath !== false && file_exists($svgPath)) {
                unlink($svgPath);
            }

            if ($pngPath !== false && file_exists($pngPath)) {
                unlink($pngPath);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $card
     */
    private function renderOgImagePngWithGd(array $card): string
    {
        $image = imagecreatetruecolor(1200, 630);

        if ($image === false) {
            abort(500, 'Unable to create result OG image canvas.');
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        imagefilledrectangle($image, 0, 0, 1199, 629, $this->allocateColor($image, '#031b0f'));
        $this->drawRoundedPanel($image, 40, 40, 1120, 550, 38, '#166534', 'rgba(255,255,255,0.10)');
        $this->drawRoundedPanel($image, 88, 76, 1024, 164, 30, 'rgba(255,255,255,0.04)');
        $this->drawRoundedPanel($image, 88, 248, 1024, 172, 34, 'rgba(255,255,255,0.08)', 'rgba(255,255,255,0.10)');
        $this->drawRoundedPanel($image, 88, 428, 508, 94, 24, 'rgba(255,255,255,0.08)', 'rgba(255,255,255,0.10)');
        $this->drawRoundedPanel($image, 604, 428, 508, 94, 24, 'rgba(255,255,255,0.08)', 'rgba(255,255,255,0.10)');

        $this->drawText($image, 120, 132, strtoupper((string) config('app.name', 'Huddersfield & District Tuesday Night Pool League')), 20, '#bbf7d0', true);
        $this->drawText($image, 120, 174, 'Match result', 40, '#ffffff', true);
        $this->drawText(
            $image,
            120,
            204,
            $this->fitOgText($image, (string) ($card['section']->name ?? 'Archived section'), 23, 780),
            23,
            '#d1fae5'
        );

        $homeTeamDisplay = $this->fitOgText($image, (string) $card['homeTeamDisplay'], 34, 600, true);
        $awayTeamDisplay = $this->fitOgText($image, (string) $card['awayTeamDisplay'], 34, 600, true);
        $this->drawText($image, 120, 324, $homeTeamDisplay, 34, '#ffffff', true);
        $this->drawText($image, 120, 374, $awayTeamDisplay, 34, '#ffffff', true);

        $this->drawRoundedPanel($image, 856, 282, 224, 104, 52, 'rgba(3,27,15,0.18)');
        $this->drawRoundedSegment($image, 860, 286, 108, 96, 48, (string) $card['homeFill'], true);
        $this->drawRoundedSegment($image, 968, 286, 108, 96, 48, (string) $card['awayFill'], false);
        imagefilledrectangle($image, 967, 286, 968, 381, $this->allocateColor($image, 'rgba(3,27,15,0.18)'));

        $this->drawCenteredText($image, 914, 348, (string) $card['result']->home_score, 48, (string) $card['homeText'], true);
        $this->drawCenteredText($image, 1022, 348, (string) $card['result']->away_score, 48, (string) $card['awayText'], true);

        $this->drawText($image, 120, 462, 'DATE', 18, '#bbf7d0', true);
        $this->drawText($image, 120, 502, (string) $card['fixture']->fixture_date->format('D j M Y'), 23, '#ffffff', true);
        $this->drawText($image, 636, 462, 'RULESET', 18, '#bbf7d0', true);
        $this->drawText(
            $image,
            636,
            502,
            $this->fitOgText($image, (string) $card['rulesetDisplay'], 23, 428, true),
            23,
            '#ffffff',
            true
        );

        $this->drawText($image, 88, 560, (string) $card['seasonDisplay'], 18, '#d1fae5');

        if ($card['submittedByDisplay']) {
            $this->drawText(
                $image,
                1112,
                560,
                $this->fitOgText($image, (string) $card['submittedByDisplay'], 15, 420),
                15,
                '#bbf7d0',
                false,
                'right'
            );
        }

        ob_start();
        imagepng($image);
        $png = ob_get_clean();
        imagedestroy($image);

        if (! is_string($png)) {
            abort(500, 'Unable to encode result OG image.');
        }

        return $png;
    }

    private function drawRoundedPanel(
        \GdImage $image,
        int $left,
        int $top,
        int $width,
        int $height,
        int $radius,
        string $fill,
        ?string $stroke = null
    ): void {
        $fillColor = $this->allocateColor($image, $fill);
        $this->fillRoundedRectangle($image, $left, $top, $width, $height, $radius, $fillColor);

        if ($stroke !== null) {
            $strokeColor = $this->allocateColor($image, $stroke);
            imagesetthickness($image, 1);
            $this->strokeRoundedRectangle($image, $left, $top, $width, $height, $radius, $strokeColor);
        }
    }

    private function drawRoundedSegment(
        \GdImage $image,
        int $left,
        int $top,
        int $width,
        int $height,
        int $radius,
        string $fill,
        bool $isLeft
    ): void {
        if ($isLeft) {
            $this->fillRoundedRectangle($image, $left, $top, $width + $radius, $height, $radius, $this->allocateColor($image, $fill));

            return;
        }

        $this->fillRoundedRectangle($image, $left - $radius, $top, $width + $radius, $height, $radius, $this->allocateColor($image, $fill));
    }

    private function drawText(
        \GdImage $image,
        int $x,
        int $y,
        string $text,
        float $fontSize,
        string $fill,
        bool $bold = false,
        string $align = 'left'
    ): void {
        $fontPath = $this->resolveOgFontPath($bold);
        $color = $this->allocateColor($image, $fill);

        if ($fontPath === null) {
            imagestring($image, 5, $x, max(0, $y - 16), $text, $color);

            return;
        }

        $box = imagettfbbox($fontSize, 0, $fontPath, $text);

        if ($box === false) {
            abort(500, 'Unable to measure result OG image text.');
        }

        $textWidth = (int) abs($box[2] - $box[0]);

        if ($align === 'center') {
            $x -= (int) round($textWidth / 2);
        } elseif ($align === 'right') {
            $x -= $textWidth;
        }

        imagettftext($image, $fontSize, 0, $x, $y, $color, $fontPath, $text);
    }

    private function drawCenteredText(
        \GdImage $image,
        int $x,
        int $baselineY,
        string $text,
        float $fontSize,
        string $fill,
        bool $bold = false
    ): void {
        $this->drawText($image, $x, $baselineY, $text, $fontSize, $fill, $bold, 'center');
    }

    private function fitOgText(\GdImage $image, string $text, float $fontSize, int $maxWidth, bool $bold = false): string
    {
        $text = trim($text);

        if ($text === '') {
            return $text;
        }

        $fontPath = $this->resolveOgFontPath($bold);

        if ($fontPath === null) {
            return $this->truncateOgText($text, max(1, (int) floor($maxWidth / 10)));
        }

        while ($text !== '') {
            $box = imagettfbbox($fontSize, 0, $fontPath, $text);

            if ($box !== false && abs($box[2] - $box[0]) <= $maxWidth) {
                return $text;
            }

            $text = rtrim(mb_substr($text, 0, max(1, mb_strlen($text) - 2))).'…';
        }

        return '';
    }

    private function resolveOgFontPath(bool $bold = false): ?string
    {
        $fontCandidates = $bold
            ? [
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
                '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf',
            ]
            : [
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
                '/usr/share/fonts/dejavu/DejaVuSans.ttf',
            ];

        foreach ($fontCandidates as $fontPath) {
            if (is_file($fontPath)) {
                return $fontPath;
            }
        }

        return null;
    }

    private function allocateColor(\GdImage $image, string $color): int
    {
        if (str_starts_with($color, '#')) {
            [$red, $green, $blue] = sscanf($color, '#%02x%02x%02x');

            return imagecolorallocatealpha($image, $red, $green, $blue, 0);
        }

        preg_match('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([0-9.]+)\)/', $color, $matches);

        if ($matches !== []) {
            $alpha = 127 - (int) round(((float) $matches[4]) * 127);

            return imagecolorallocatealpha($image, (int) $matches[1], (int) $matches[2], (int) $matches[3], $alpha);
        }

        return imagecolorallocatealpha($image, 255, 255, 255, 0);
    }

    private function fillRoundedRectangle(\GdImage $image, int $left, int $top, int $width, int $height, int $radius, int $color): void
    {
        imagefilledrectangle($image, $left + $radius, $top, $left + $width - $radius - 1, $top + $height - 1, $color);
        imagefilledrectangle($image, $left, $top + $radius, $left + $width - 1, $top + $height - $radius - 1, $color);
        imagefilledellipse($image, $left + $radius, $top + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $left + $width - $radius, $top + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $left + $radius, $top + $height - $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $left + $width - $radius, $top + $height - $radius, $radius * 2, $radius * 2, $color);
    }

    private function strokeRoundedRectangle(\GdImage $image, int $left, int $top, int $width, int $height, int $radius, int $color): void
    {
        imageline($image, $left + $radius, $top, $left + $width - $radius, $top, $color);
        imageline($image, $left + $radius, $top + $height, $left + $width - $radius, $top + $height, $color);
        imageline($image, $left, $top + $radius, $left, $top + $height - $radius, $color);
        imageline($image, $left + $width, $top + $radius, $left + $width, $top + $height - $radius, $color);
        imagearc($image, $left + $radius, $top + $radius, $radius * 2, $radius * 2, 180, 270, $color);
        imagearc($image, $left + $width - $radius, $top + $radius, $radius * 2, $radius * 2, 270, 360, $color);
        imagearc($image, $left + $radius, $top + $height - $radius, $radius * 2, $radius * 2, 90, 180, $color);
        imagearc($image, $left + $width - $radius, $top + $height - $radius, $radius * 2, $radius * 2, 0, 90, $color);
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
