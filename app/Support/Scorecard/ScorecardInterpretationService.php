<?php

namespace App\Support\Scorecard;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class ScorecardInterpretationService
{
    /**
     * Interpret a scorecard image and return a structured extraction result.
     *
     * When no API key is configured a graceful fallback result is returned so that
     * the existing form workflow is never damaged.
     */
    public function interpret(UploadedFile $file): ScorecardExtractionResult
    {
        $apiKey = config('services.openai.api_key');
        $model = config('services.openai.model', 'gpt-4o');

        if (empty($apiKey)) {
            return new ScorecardExtractionResult(
                frames: [],
                warnings: ['Scorecard scanning is not configured. Please enter frames manually.'],
            );
        }

        // Guard against oversized files. The Livewire component validates max:10240 (kilobytes = 10 MB)
        // before calling this method, but we defensively enforce the same limit here as well.
        if ($file->getSize() === false || $file->getSize() > 10 * 1024 * 1024) {
            return new ScorecardExtractionResult(
                frames: [],
                warnings: ['Scorecard image is too large to process. Please use a smaller photo.'],
            );
        }

        $imageData = base64_encode((string) file_get_contents($file->getRealPath()));
        $mimeType = $file->getMimeType() ?? 'image/jpeg';

        $response = Http::withToken($apiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $this->buildPrompt(),
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$mimeType};base64,{$imageData}",
                                    'detail' => 'high',
                                ],
                            ],
                        ],
                    ],
                ],
                'max_tokens' => 1000,
            ]);

        if (! $response->successful()) {
            return new ScorecardExtractionResult(
                frames: [],
                warnings: ['Scorecard scanning failed. Please enter frames manually.'],
            );
        }

        return $this->parseResponse($response->json('choices.0.message.content', '{}'));
    }

    private function buildPrompt(): string
    {
        return <<<'PROMPT'
You are analysing a photo of a pool match scorecard. Extract the frame-by-frame results.

Return a JSON object with this exact structure:
{
  "frames": {
    "1": { "home_player_name": "...", "away_player_name": "...", "home_score": 0, "away_score": 1 },
    "2": { ... },
    ...up to "10"
  },
  "warnings": ["any issues or unclear data"]
}

Rules:
- Frames are numbered 1 to 10 (include only those you can read)
- home_score and away_score must each be 0 or 1, and exactly one must be 1 per frame
- Use the actual player names written on the scorecard
- If a player name is unclear or illegible, use null and add a warning
- If a score is unclear, use 0 and add a warning
- Only return frames you can read; omit frames with no data
PROMPT;
    }

    private function parseResponse(string $content): ScorecardExtractionResult
    {
        $data = json_decode($content, true);

        if (! is_array($data)) {
            return new ScorecardExtractionResult(
                frames: [],
                warnings: ['Could not parse scorecard data. Please enter frames manually.'],
            );
        }

        $rawFrames = $data['frames'] ?? [];
        $warnings = array_values(array_filter(
            (array) ($data['warnings'] ?? []),
            fn ($w) => is_string($w) && $w !== '',
        ));

        if (! is_array($rawFrames) || empty($rawFrames)) {
            return new ScorecardExtractionResult(
                frames: [],
                warnings: array_merge($warnings, ['No frame data found in image. Please enter frames manually.']),
            );
        }

        $frames = [];

        foreach ($rawFrames as $frameNum => $frame) {
            if (! is_array($frame)) {
                continue;
            }

            $num = (int) $frameNum;

            if ($num < 1 || $num > 10) {
                continue;
            }

            $frames[$num] = [
                'home_player_name' => is_string($frame['home_player_name'] ?? null) ? $frame['home_player_name'] : null,
                'away_player_name' => is_string($frame['away_player_name'] ?? null) ? $frame['away_player_name'] : null,
                'home_score' => in_array((int) ($frame['home_score'] ?? 0), [0, 1], true) ? (int) $frame['home_score'] : 0,
                'away_score' => in_array((int) ($frame['away_score'] ?? 0), [0, 1], true) ? (int) $frame['away_score'] : 0,
            ];
        }

        return new ScorecardExtractionResult(frames: $frames, warnings: $warnings);
    }
}
