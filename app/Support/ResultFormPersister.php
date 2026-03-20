<?php

namespace App\Support;

use App\Models\Fixture;
use App\Models\Frame;
use App\Models\Result;
use Illuminate\Support\Facades\DB;

class ResultFormPersister
{
    /**
     * @param  array<int, array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}>  $frames
     */
    public function persist(Fixture $fixture, ?Result $result, array $frames, bool $lock): Result
    {
        $isOverridden = $result?->is_overridden ?? 0;
        $scores = $this->scoresFromFrames($frames);

        return DB::transaction(function () use ($fixture, $result, $frames, $lock, $isOverridden, $scores) {
            $attributes = [
                'home_score' => $scores['home_score'],
                'away_score' => $scores['away_score'],
                'is_confirmed' => $lock,
                'is_overridden' => $isOverridden,
                'section_id' => $fixture->section_id,
                'ruleset_id' => $fixture->ruleset_id,
            ];

            if ($lock) {
                $attributes['submitted_by'] = auth()->id();
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

            $this->syncPersistedFrames($result, array_values($frames));

            return $result->fresh(['frames' => fn ($query) => $query->orderBy('id')]);
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
}
