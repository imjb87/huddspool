<?php

namespace App\Livewire\Forms;

use App\Models\Frame;
use App\Models\Result;
use App\Rules\BothPlayersAwardedIfOneIs;
use App\Rules\FrameScoreEqualsOne;
use App\Rules\FrameScoresAddUpToTen;
use App\Rules\PlayerLimit;
use App\Rules\TotalScoresAddUpToTen;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Form;

class FixtureResultForm extends Form
{
    /**
     * @var array<int, array{home_player_id: int|string|null, away_player_id: int|string|null, home_score: int|string, away_score: int|string}>
     */
    public array $frames = [];

    #[Locked]
    public int $homeScore = 0;

    #[Locked]
    public int $awayScore = 0;

    #[Locked]
    public int $totalScore = 0;

    public function updatedFrames($value, $key): void
    {
        $this->syncOpposingFrameScore($value, $key);
        $this->recalculateScores();
    }

    public function syncFromResult(?Result $result): void
    {
        $this->syncFromResultAndDraft($result);
    }

    /**
     * @param  array<int, array{home_player_id?: int|string|null, away_player_id?: int|string|null, home_score?: int|string|null, away_score?: int|string|null}>|null  $draftFrames
     */
    public function syncFromResultAndDraft(?Result $result, ?array $draftFrames = null): void
    {
        $state = $result
            ? $this->mapFramesToState($result->frames->sortBy('id')->values())
            : $this->emptyFrames();

        if ($draftFrames !== null) {
            $state = $this->mergeDraftFramesIntoState($state, $draftFrames);
        }

        $this->frames = $state;
        $this->recalculateScores();
    }

    /**
     * @return array<int, array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}>
     */
    public function prepareFrames(bool $requireComplete, bool $allowEmpty = false): array
    {
        $frames = [];
        $errors = [];

        for ($i = 1; $i <= 10; $i++) {
            $frame = $this->frames[$i] ?? [
                'home_player_id' => null,
                'away_player_id' => null,
                'home_score' => 0,
                'away_score' => 0,
            ];

            $homePlayer = $this->normalizePlayerId($frame['home_player_id'] ?? null);
            $awayPlayer = $this->normalizePlayerId($frame['away_player_id'] ?? null);
            $homeScore = $this->normalizeScore($frame['home_score'] ?? 0);
            $awayScore = $this->normalizeScore($frame['away_score'] ?? 0);

            $hasAnyInput = $homePlayer !== null || $awayPlayer !== null || $homeScore > 0 || $awayScore > 0;
            $scoresAreValid = in_array($homeScore, [0, 1], true) && in_array($awayScore, [0, 1], true);
            $isComplete = $homePlayer !== null && $awayPlayer !== null && $scoresAreValid && ($homeScore + $awayScore) === 1;

            if (! $isComplete) {
                if ($requireComplete && $hasAnyInput) {
                    $errors["form.frames.$i"] = 'Complete every frame before submitting.';
                }

                continue;
            }

            $frames[$i] = [
                'home_player_id' => $homePlayer,
                'away_player_id' => $awayPlayer,
                'home_score' => $homeScore,
                'away_score' => $awayScore,
            ];
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        if (empty($frames) && ! $allowEmpty) {
            throw ValidationException::withMessages([
                'form.frames' => 'Add at least one completed frame before saving.',
            ]);
        }

        if (! empty($frames)) {
            $this->validateSharedRules($frames);
        }

        if ($requireComplete) {
            if (count($frames) !== 10) {
                throw ValidationException::withMessages([
                    'form.frames' => 'All 10 frames must be completed before submitting.',
                ]);
            }

            $this->validateLockSpecificRules($frames);
        }

        return $frames;
    }

    /**
     * @param  array<int, array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}>  $frames
     */
    public function matchesExistingFrames(?Result $result, array $frames): bool
    {
        if (! $result) {
            return empty($frames);
        }

        $existing = $result->relationLoaded('frames')
            ? $result->frames
            : $result->frames()->orderBy('id')->get();

        $existing = $existing->values();
        $payload = array_values($frames);

        if ($existing->count() !== count($payload)) {
            return false;
        }

        foreach ($payload as $index => $frame) {
            $existingFrame = $existing[$index] ?? null;

            if (! $existingFrame) {
                return false;
            }

            if (
                (int) $existingFrame->home_player_id !== $frame['home_player_id'] ||
                (int) $existingFrame->away_player_id !== $frame['away_player_id'] ||
                (int) $existingFrame->home_score !== $frame['home_score'] ||
                (int) $existingFrame->away_score !== $frame['away_score']
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<int, array{home_player_id: int|string|null, away_player_id: int|string|null, home_score: int, away_score: int}>
     */
    public function draftFrames(): array
    {
        $draftFrames = [];

        for ($i = 1; $i <= 10; $i++) {
            $frame = $this->frames[$i] ?? [];

            $draftFrames[$i] = [
                'home_player_id' => $frame['home_player_id'] ?? null,
                'away_player_id' => $frame['away_player_id'] ?? null,
                'home_score' => $this->normalizeScore($frame['home_score'] ?? 0),
                'away_score' => $this->normalizeScore($frame['away_score'] ?? 0),
            ];
        }

        return $draftFrames;
    }

    public function draftFramesAreEmpty(): bool
    {
        foreach ($this->draftFrames() as $frame) {
            if (($frame['home_player_id'] ?? null) !== null && ($frame['home_player_id'] ?? null) !== '') {
                return false;
            }

            if (($frame['away_player_id'] ?? null) !== null && ($frame['away_player_id'] ?? null) !== '') {
                return false;
            }

            if (($frame['home_score'] ?? 0) > 0 || ($frame['away_score'] ?? 0) > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<int|string, array{home_player_id?: int|string|null, away_player_id?: int|string|null, home_score?: int|string|null, away_score?: int|string|null}>|null  $draftFrames
     */
    public function matchesDraftState(?array $draftFrames): bool
    {
        return $this->draftFrames() === $this->normalizeDraftFrames($draftFrames);
    }

    private function validateSharedRules(array $frames): void
    {
        $rules = [
            new BothPlayersAwardedIfOneIs($frames),
            new FrameScoreEqualsOne($frames),
            new PlayerLimit($frames),
        ];

        foreach ($rules as $rule) {
            if (! $rule->passes('frames', $frames)) {
                throw ValidationException::withMessages([
                    'form.frames' => $rule->message(),
                ]);
            }
        }
    }

    private function validateLockSpecificRules(array $frames): void
    {
        $totalFramesScore = array_reduce($frames, function ($total, $frame) {
            return $total + $frame['home_score'] + $frame['away_score'];
        }, 0);

        $lockRules = [
            new FrameScoresAddUpToTen($frames),
            new TotalScoresAddUpToTen,
        ];

        foreach ($lockRules as $rule) {
            $attribute = $rule instanceof TotalScoresAddUpToTen ? 'form.totalScore' : 'form.frames';
            $value = $rule instanceof TotalScoresAddUpToTen ? $totalFramesScore : $frames;

            if (! $rule->passes($attribute, $value)) {
                throw ValidationException::withMessages([
                    'form.frames' => $rule->message(),
                ]);
            }
        }
    }

    private function normalizePlayerId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function normalizeScore(mixed $value): int
    {
        return (int) $value;
    }

    private function syncOpposingFrameScore(mixed $value, mixed $key): void
    {
        if (! is_string($key) || ! is_numeric($value) || (int) $value !== 1) {
            return;
        }

        if (! preg_match('/^(?<frame>\d+)\.(?<side>home|away)_score$/', $key, $matches)) {
            return;
        }

        $frameNumber = (int) $matches['frame'];
        $opposingSide = $matches['side'] === 'home' ? 'away' : 'home';

        $this->frames[$frameNumber]["{$opposingSide}_score"] = 0;
    }

    /**
     * @param  Collection<int, Frame>  $frames
     * @return array<int, array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}>
     */
    private function mapFramesToState(Collection $frames): array
    {
        $state = [];

        for ($i = 1; $i <= 10; $i++) {
            $frame = $frames[$i - 1] ?? null;

            $state[$i] = [
                'home_player_id' => $frame?->home_player_id,
                'away_player_id' => $frame?->away_player_id,
                'home_score' => $frame?->home_score ?? 0,
                'away_score' => $frame?->away_score ?? 0,
            ];
        }

        return $state;
    }

    /**
     * @return array<int, array{home_player_id: null, away_player_id: null, home_score: int, away_score: int}>
     */
    private function emptyFrames(): array
    {
        $frames = [];

        for ($i = 1; $i <= 10; $i++) {
            $frames[$i] = [
                'home_player_id' => null,
                'away_player_id' => null,
                'home_score' => 0,
                'away_score' => 0,
            ];
        }

        return $frames;
    }

    /**
     * @param  array<int, array{home_player_id: int|string|null, away_player_id: int|string|null, home_score: int|string, away_score: int|string}>  $state
     * @param  array<int, array{home_player_id?: int|string|null, away_player_id?: int|string|null, home_score?: int|string|null, away_score?: int|string|null}>  $draftFrames
     * @return array<int, array{home_player_id: int|string|null, away_player_id: int|string|null, home_score: int|string, away_score: int|string}>
     */
    private function mergeDraftFramesIntoState(array $state, array $draftFrames): array
    {
        for ($i = 1; $i <= 10; $i++) {
            $draftFrame = $draftFrames[$i] ?? $draftFrames[(string) $i] ?? null;

            if (! is_array($draftFrame)) {
                continue;
            }

            $state[$i] = [
                'home_player_id' => $draftFrame['home_player_id'] ?? null,
                'away_player_id' => $draftFrame['away_player_id'] ?? null,
                'home_score' => $draftFrame['home_score'] ?? 0,
                'away_score' => $draftFrame['away_score'] ?? 0,
            ];
        }

        return $state;
    }

    /**
     * @param  array<int|string, array{home_player_id?: int|string|null, away_player_id?: int|string|null, home_score?: int|string|null, away_score?: int|string|null}>|null  $draftFrames
     * @return array<int, array{home_player_id: int|string|null, away_player_id: int|string|null, home_score: int, away_score: int}>
     */
    private function normalizeDraftFrames(?array $draftFrames): array
    {
        return $this->mergeDraftFramesIntoState($this->emptyFrames(), $draftFrames ?? []);
    }

    private function recalculateScores(): void
    {
        $this->homeScore = array_sum(array_column($this->frames, 'home_score'));
        $this->awayScore = array_sum(array_column($this->frames, 'away_score'));
        $this->totalScore = $this->homeScore + $this->awayScore;
    }
}
