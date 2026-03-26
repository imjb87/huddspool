<?php

namespace App\Support;

use App\Models\Fixture;
use App\Models\Result;

class ResultDraftPayloadFactory
{
    /**
     * @return array{
     *     fixture_id: int,
     *     result_id: int,
     *     draft_version: int,
     *     frames: array<int, array{home_player_id: int|string|null, away_player_id: int|string|null, home_score: int, away_score: int}>,
     *     home_score: int,
     *     away_score: int,
     *     updated_by_id: ?int,
     *     updated_by_name: ?string,
     *     client_id: string,
     *     is_confirmed: bool,
     *     result_url: string
     * }
     */
    public function make(Fixture $fixture, Result $result, string $clientId): array
    {
        $result->loadMissing([
            'frames' => fn ($query) => $query->orderBy('id'),
            'draftUpdatedBy',
        ]);

        return [
            'fixture_id' => (int) $fixture->getKey(),
            'result_id' => (int) $result->getKey(),
            'draft_version' => (int) $result->draft_version,
            'frames' => $this->frames($result),
            'home_score' => (int) $result->home_score,
            'away_score' => (int) $result->away_score,
            'updated_by_id' => $result->draft_updated_by ? (int) $result->draft_updated_by : null,
            'updated_by_name' => $result->draftUpdatedBy?->name,
            'client_id' => $clientId,
            'is_confirmed' => (bool) $result->is_confirmed,
            'result_url' => route('result.show', $result),
        ];
    }

    /**
     * @return array<int, array{home_player_id: int|string|null, away_player_id: int|string|null, home_score: int, away_score: int}>
     */
    private function frames(Result $result): array
    {
        $frames = [];
        $draftState = is_array($result->draft_state) ? $result->draft_state : [];

        for ($i = 1; $i <= 10; $i++) {
            $draftFrame = $draftState[$i] ?? $draftState[(string) $i] ?? null;
            $persistedFrame = $result->frames[$i - 1] ?? null;

            $frames[$i] = [
                'home_player_id' => $draftFrame['home_player_id'] ?? $persistedFrame?->home_player_id,
                'away_player_id' => $draftFrame['away_player_id'] ?? $persistedFrame?->away_player_id,
                'home_score' => (int) ($draftFrame['home_score'] ?? $persistedFrame?->home_score ?? 0),
                'away_score' => (int) ($draftFrame['away_score'] ?? $persistedFrame?->away_score ?? 0),
            ];
        }

        return $frames;
    }
}
