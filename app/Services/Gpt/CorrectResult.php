<?php

namespace App\Services\Gpt;

use App\Models\Frame;
use App\Models\GptActionAudit;
use App\Models\Result;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CorrectResult
{
    /** @param array<int, array{home_player_id: int, away_player_id: int, home_score: int, away_score: int}> $frames */
    public function handle(User $administrator, Result $result, array $frames, int $expectedDraftVersion, string $reason, ?string $ipAddress, ?string $userAgent): GptActionAudit
    {
        return DB::transaction(function () use ($administrator, $result, $frames, $expectedDraftVersion, $reason, $ipAddress, $userAgent): GptActionAudit {
            $lockedResult = Result::query()->with('fixture')->lockForUpdate()->findOrFail($result->id);

            if ((int) $lockedResult->draft_version !== $expectedDraftVersion) {
                throw ValidationException::withMessages(['expected_draft_version' => 'The result changed after it was inspected. Inspect the result and frames again before retrying.']);
            }

            $frames = array_values($frames);
            $this->validateFrames($lockedResult, $frames);
            $beforeFrames = $lockedResult->frames()->orderBy('id')->get()->map(fn (Frame $frame): array => $this->frameData($frame))->all();
            $before = $this->resultData($lockedResult, $beforeFrames, $reason);

            $homeScore = array_sum(array_column($frames, 'home_score'));
            $awayScore = array_sum(array_column($frames, 'away_score'));
            $lockedResult->update([
                'home_score' => $homeScore,
                'away_score' => $awayScore,
                'is_confirmed' => true,
                'is_overridden' => true,
                'draft_version' => $expectedDraftVersion + 1,
                'draft_updated_by' => $administrator->id,
                'draft_state' => array_combine(range(1, 10), $frames),
            ]);
            $this->syncFrames($lockedResult, $frames);

            return GptActionAudit::query()->create([
                'administrator_id' => $administrator->id,
                'action' => 'correct_result',
                'subject_type' => Result::class,
                'subject_id' => $lockedResult->id,
                'before' => $before,
                'after' => $this->resultData($lockedResult->refresh(), $frames, $reason),
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
        });
    }

    private function validateFrames(Result $result, array $frames): void
    {
        $fixture = $result->fixture;
        $existingFrames = $result->frames()->get();
        $existingHomePlayerIds = $existingFrames->pluck('home_player_id')->map(fn ($id): int => (int) $id)->all();
        $existingAwayPlayerIds = $existingFrames->pluck('away_player_id')->map(fn ($id): int => (int) $id)->all();
        $homePlayers = User::withTrashed()->whereIn('id', array_column($frames, 'home_player_id'))->get()->keyBy('id');
        $awayPlayers = User::withTrashed()->whereIn('id', array_column($frames, 'away_player_id'))->get()->keyBy('id');
        $appearances = [];

        foreach ($frames as $index => $frame) {
            if ((int) $frame['home_score'] + (int) $frame['away_score'] !== 1) {
                throw ValidationException::withMessages(["frames.$index" => 'Each frame must have exactly one winner.']);
            }

            if ((int) $homePlayers->get($frame['home_player_id'])?->team_id !== (int) $fixture->home_team_id
                && ! in_array((int) $frame['home_player_id'], $existingHomePlayerIds, true)) {
                throw ValidationException::withMessages(["frames.$index.home_player_id" => 'The home player must belong to the fixture’s home team.']);
            }

            if ((int) $awayPlayers->get($frame['away_player_id'])?->team_id !== (int) $fixture->away_team_id
                && ! in_array((int) $frame['away_player_id'], $existingAwayPlayerIds, true)) {
                throw ValidationException::withMessages(["frames.$index.away_player_id" => 'The away player must belong to the fixture’s away team.']);
            }

            foreach (['home_player_id', 'away_player_id'] as $field) {
                $appearances[$frame[$field]] = ($appearances[$frame[$field]] ?? 0) + 1;
            }
        }

        if (max($appearances) > 2) {
            throw ValidationException::withMessages(['frames' => 'A player cannot play more than twice.']);
        }
    }

    private function syncFrames(Result $result, array $frames): void
    {
        $existing = $result->frames()->orderBy('id')->get()->values();

        foreach ($frames as $index => $frame) {
            $current = $existing->get($index);
            $current ? $current->update($frame) : $result->frames()->create($frame);
        }

        $existing->slice(count($frames))->each->delete();
    }

    private function frameData(Frame $frame): array
    {
        return $frame->only(['home_player_id', 'away_player_id', 'home_score', 'away_score']);
    }

    private function resultData(Result $result, array $frames, string $reason): array
    {
        return ['home_score' => $result->home_score, 'away_score' => $result->away_score, 'draft_version' => $result->draft_version, 'frames' => $frames, 'reason' => $reason];
    }
}
