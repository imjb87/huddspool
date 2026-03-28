<?php

namespace App\Support;

use App\Exceptions\StaleResultDraftException;
use App\Models\Fixture;
use App\Models\Frame;
use App\Models\Result;
use Illuminate\Support\Facades\DB;

class ResultFormPersister
{
    /**
     * @param  array<int, array{home_player_id: int|string|null, away_player_id: int|string|null, home_score: int, away_score: int}>  $draftFrames
     */
    public function persistDraft(Fixture $fixture, ?Result $result, array $draftFrames, int $updatedBy, ?int $expectedDraftVersion = null): Result
    {
        return $this->persist(
            fixture: $fixture,
            result: $result,
            draftFrames: $draftFrames,
            completedFrames: $this->completedFramesFromDraft($draftFrames),
            confirmResult: false,
            updatedBy: $updatedBy,
            expectedDraftVersion: $expectedDraftVersion,
        );
    }

    /**
     * @param  array<int, array{home_player_id: int|string|null, away_player_id: int|string|null, home_score: int, away_score: int}>  $draftFrames
     * @param  array<int, array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}>  $completedFrames
     */
    public function submit(Fixture $fixture, ?Result $result, array $draftFrames, array $completedFrames, int $updatedBy, ?int $expectedDraftVersion = null): Result
    {
        return $this->persist(
            fixture: $fixture,
            result: $result,
            draftFrames: $draftFrames,
            completedFrames: $completedFrames,
            confirmResult: true,
            updatedBy: $updatedBy,
            expectedDraftVersion: $expectedDraftVersion,
        );
    }

    /**
     * @param  array<int, array{home_player_id: int|string|null, away_player_id: int|string|null, home_score: int, away_score: int}>  $draftFrames
     * @param  array<int, array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}>  $completedFrames
     */
    private function persist(Fixture $fixture, ?Result $result, array $draftFrames, array $completedFrames, bool $confirmResult, int $updatedBy, ?int $expectedDraftVersion = null): Result
    {
        $scores = $this->scoresFromFrames($completedFrames);

        return DB::transaction(function () use ($fixture, $result, $draftFrames, $completedFrames, $confirmResult, $updatedBy, $expectedDraftVersion, $scores) {
            Fixture::query()
                ->whereKey($fixture->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $result = Result::query()
                ->where('fixture_id', $fixture->getKey())
                ->lockForUpdate()
                ->first();

            if ($result && $expectedDraftVersion !== null && (int) $result->draft_version !== $expectedDraftVersion) {
                throw new StaleResultDraftException($result->fresh([
                    'frames' => fn ($query) => $query->orderBy('id'),
                    'draftUpdatedBy',
                ]));
            }

            $attributes = [
                'home_score' => $scores['home_score'],
                'away_score' => $scores['away_score'],
                'is_confirmed' => $confirmResult,
                'is_overridden' => $result?->is_overridden ?? false,
                'draft_version' => (int) ($result?->draft_version ?? 0) + 1,
                'draft_updated_by' => $updatedBy,
                'draft_state' => $draftFrames,
                'section_id' => $fixture->section_id,
                'ruleset_id' => $fixture->ruleset_id,
            ];

            if ($confirmResult) {
                $attributes['submitted_by'] = $updatedBy;
                $attributes['submitted_at'] = now();
            }

            if (! $result) {
                $result = Result::create(array_merge($attributes, [
                    'fixture_id' => $fixture->id,
                    'home_team_id' => $fixture->homeTeam->id,
                    'home_team_name' => $fixture->homeTeam->name,
                    'away_team_id' => $fixture->awayTeam->id,
                    'away_team_name' => $fixture->awayTeam->name,
                ]));
            } else {
                $result->update($attributes);
            }

            $this->syncPersistedFrames($result, array_values($completedFrames));

            return $result->fresh([
                'frames' => fn ($query) => $query->orderBy('id'),
                'draftUpdatedBy',
            ]);
        });
    }

    /**
     * @param  array<int, array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}>  $frames
     * @return array{home_score: int, away_score: int}
     */
    private function scoresFromFrames(array $frames): array
    {
        return [
            'home_score' => array_sum(array_column($frames, 'home_score')),
            'away_score' => array_sum(array_column($frames, 'away_score')),
        ];
    }

    /**
     * @param  array<int, array{home_player_id: int|string|null, away_player_id: int|string|null, home_score: int, away_score: int}>  $draftFrames
     * @return array<int, array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}>
     */
    private function completedFramesFromDraft(array $draftFrames): array
    {
        $frames = [];

        foreach ($draftFrames as $frame) {
            $homePlayerId = $this->normalizePlayerId($frame['home_player_id'] ?? null);
            $awayPlayerId = $this->normalizePlayerId($frame['away_player_id'] ?? null);
            $homeScore = (int) ($frame['home_score'] ?? 0);
            $awayScore = (int) ($frame['away_score'] ?? 0);

            $isComplete = $homePlayerId !== null
                && $awayPlayerId !== null
                && in_array($homeScore, [0, 1], true)
                && in_array($awayScore, [0, 1], true)
                && ($homeScore + $awayScore) === 1;

            if (! $isComplete) {
                continue;
            }

            $frames[] = [
                'home_player_id' => $homePlayerId,
                'away_player_id' => $awayPlayerId,
                'home_score' => $homeScore,
                'away_score' => $awayScore,
            ];
        }

        return $frames;
    }

    /**
     * @param  array<int, array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}>  $frames
     */
    private function syncPersistedFrames(Result $result, array $frames): void
    {
        $existingFrames = $result->frames()->orderBy('id')->get()->values();

        foreach ($frames as $index => $frame) {
            $existingFrame = $existingFrames[$index] ?? null;

            if ($existingFrame instanceof Frame) {
                $existingFrame->update($frame);

                continue;
            }

            $result->frames()->create($frame);
        }

        $existingFrames
            ->slice(count($frames))
            ->each
            ->delete();
    }

    private function normalizePlayerId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
