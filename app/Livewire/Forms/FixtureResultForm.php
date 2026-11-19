<?php

namespace App\Livewire\Forms;

use App\Domain\Results\ResultFrameState;
use App\Models\Result;
use App\Rules\BothPlayersAwardedIfOneIs;
use App\Rules\FrameScoreEqualsOne;
use App\Rules\FrameScoresAddUpToTen;
use App\Rules\PlayerLimit;
use App\Rules\TotalScoresAddUpToTen;
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

    public function toggleFrameScore(int $frameNumber, string $side): bool
    {
        if ($frameNumber < 1 || $frameNumber > ResultFrameState::FRAME_COUNT || ! in_array($side, ['home', 'away'], true)) {
            return false;
        }

        $selectedScoreKey = "{$side}_score";
        $opposingSide = $side === 'home' ? 'away' : 'home';
        $opposingScoreKey = "{$opposingSide}_score";
        $selectedScore = (int) data_get($this->frames, "{$frameNumber}.{$selectedScoreKey}", 0);

        $this->frames[$frameNumber][$selectedScoreKey] = $selectedScore === 1 ? 0 : 1;
        $this->frames[$frameNumber][$opposingScoreKey] = 0;

        $this->recalculateScores();

        return true;
    }

    /**
     * @param  array<int, array{home_player_id?: int|string|null, away_player_id?: int|string|null, home_score?: int|string|null, away_score?: int|string|null}>|null  $draftFrames
     */
    public function syncFromResultAndDraft(?Result $result, ?array $draftFrames = null): void
    {
        $state = $result
            ? ResultFrameState::fromPersistedFrames($result->frames->sortBy('id')->values())
            : ResultFrameState::empty();

        if ($draftFrames !== null) {
            $state = ResultFrameState::merge($state, $draftFrames);
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

        foreach (ResultFrameState::normalize($this->frames) as $i => $frame) {
            $hasAnyInput = $frame['home_player_id'] !== null
                || $frame['away_player_id'] !== null
                || $frame['home_score'] > 0
                || $frame['away_score'] > 0;

            if (! ResultFrameState::isComplete($frame)) {
                if ($requireComplete && $hasAnyInput) {
                    $errors["form.frames.$i"] = 'Complete every frame before submitting.';
                }

                continue;
            }

            $frames[$i] = $frame;
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
            if (count($frames) !== ResultFrameState::FRAME_COUNT) {
                throw ValidationException::withMessages([
                    'form.frames' => sprintf('All %d frames must be completed before submitting.', ResultFrameState::FRAME_COUNT),
                ]);
            }

            $this->validateLockSpecificRules($frames);
        }

        return $frames;
    }

    /**
     * @return array<int, array{home_player_id: int|string|null, away_player_id: int|string|null, home_score: int, away_score: int}>
     */
    public function draftFrames(): array
    {
        return ResultFrameState::normalize($this->frames);
    }

    public function draftFramesAreEmpty(): bool
    {
        return ResultFrameState::isEmpty($this->frames);
    }

    /**
     * @param  array<int|string, array{home_player_id?: int|string|null, away_player_id?: int|string|null, home_score?: int|string|null, away_score?: int|string|null}>|null  $draftFrames
     */
    public function matchesDraftState(?array $draftFrames): bool
    {
        return $this->draftFrames() === ResultFrameState::normalize($draftFrames);
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
        $totalFramesScore = ResultFrameState::scores($frames)['total_score'];

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

    private function recalculateScores(): void
    {
        $scores = ResultFrameState::scores($this->frames);
        $this->homeScore = $scores['home_score'];
        $this->awayScore = $scores['away_score'];
        $this->totalScore = $scores['total_score'];
    }
}
