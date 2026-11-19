<?php

namespace App\Domain\Results;

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
        $draftState = is_array($result->draft_state) ? $result->draft_state : [];
        $frames = ResultFrameState::fromPersistedFrames($result->frames->sortBy('id')->values());

        return ResultFrameState::merge($frames, $draftState);
    }
}
