<?php

namespace App\Livewire;

use App\Models\Fixture;
use App\Models\FixtureResultLock;
use App\Models\Frame;
use App\Models\Result;
use App\Rules\BothPlayersAwardedIfOneIs;
use App\Rules\FrameScoreEqualsOne;
use App\Rules\FrameScoresAddUpToTen;
use App\Rules\PlayerLimit;
use App\Rules\TotalScoresAddUpToTen;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ResultForm extends Component
{
    public Fixture $fixture;

    public ?Result $result = null;

    public array $frames = [];

    #[Locked]
    public int $homeScore = 0;

    #[Locked]
    public int $awayScore = 0;

    #[Locked]
    public int $totalScore = 0;

    #[Locked]
    public bool $isLocked = false;

    #[Locked]
    public bool $canEdit = false;

    #[Locked]
    public bool $lockedByAnother = false;

    #[Locked]
    public ?string $lockOwnerName = null;

    #[Locked]
    public ?string $lockExpiresAtHuman = null;

    protected ?FixtureResultLock $lock = null;

    protected int $lockTimeoutMinutes = 10;

    public function mount(Fixture $fixture): void
    {
        $this->fixture = $this->loadFixture($fixture);

        $this->guardAccess();
        $this->hydrateResultState();
        $this->initializeLock();
    }

    public function updatedFrames($value, $key): void
    {
        $this->handleFrameUpdate();
    }

    public function submit()
    {
        $this->refreshActionState();

        if ($this->isLocked) {
            return redirect()->route('result.show', $this->result);
        }

        if (! $this->canEdit) {
            return $this->redirectToFixtureOrResult();
        }

        $frames = $this->prepareFrames(requireComplete: true);
        $result = $this->persistFrames($frames, lock: true);

        $this->syncComponentState($result);
        $this->releaseLock();

        sleep(1);

        return redirect()->route('result.show', $this->result);
    }

    public function keepLockAlive(): void
    {
        $this->refreshActionState();
    }

    public function render()
    {
        return view('livewire.result-form');
    }

    private function guardAccess(): void
    {
        if ($this->isHomeOrAwayTeam(1)) {
            abort(404);
        }

        Gate::authorize('submitResult', $this->fixture);

        if ($this->fixture->fixture_date->gte(now())) {
            abort(404);
        }

        $this->result = $this->fixture->result;

        if ($this->result && $this->result->is_confirmed) {
            abort(404);
        }
    }

    private function loadFixture(Fixture $fixture): Fixture
    {
        return Fixture::query()
            ->with([
                'section',
                'venue',
                'homeTeam.players',
                'awayTeam.players',
                'result.frames' => fn ($query) => $query->orderBy('id'),
            ])
            ->findOrFail($fixture->getKey());
    }

    private function refreshActionState(): void
    {
        $this->fixture = $this->loadFixture($this->fixture);

        if ($this->isHomeOrAwayTeam(1)) {
            abort(404);
        }

        Gate::authorize('submitResult', $this->fixture);

        if ($this->fixture->fixture_date->gte(now())) {
            abort(404);
        }

        $this->result = $this->fixture->result;
        $this->isLocked = (bool) ($this->result?->is_confirmed ?? false);

        if ($this->isLocked) {
            $this->clearLockState();

            return;
        }

        $this->initializeLock();
    }

    private function hydrateResultState(): void
    {
        $this->syncResultState($this->fixture->result);
    }

    private function syncComponentState(Result $result): void
    {
        $this->syncResultState($result->load(['frames' => fn ($query) => $query->orderBy('id')]));
    }

    private function handleFrameUpdate(): void
    {
        $this->recalculateScores();
        $this->refreshActionState();

        if ($this->isLocked || ! $this->canEdit) {
            return;
        }

        try {
            $frames = $this->prepareFrames(requireComplete: false, allowEmpty: true);
        } catch (ValidationException $exception) {
            throw $exception;
        }

        if ($this->result === null && empty($frames)) {
            return;
        }

        if ($this->framesMatchExisting($frames)) {
            return;
        }

        $result = $this->persistFrames($frames, lock: false);

        $this->result = $result;
        $this->isLocked = (bool) $result->is_confirmed;
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
                    'frames' => $rule->message(),
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
            $attribute = $rule instanceof TotalScoresAddUpToTen ? 'totalScore' : 'frames';
            $value = $rule instanceof TotalScoresAddUpToTen ? $totalFramesScore : $frames;

            if (! $rule->passes($attribute, $value)) {
                throw ValidationException::withMessages([
                    $attribute === 'totalScore' ? 'frames' : $attribute => $rule->message(),
                ]);
            }
        }
    }

    private function persistFrames(array $frames, bool $lock): Result
    {
        $homeScore = array_sum(array_column($frames, 'home_score'));
        $awayScore = array_sum(array_column($frames, 'away_score'));

        $isOverridden = $this->result?->is_overridden ?? 0;

        return DB::transaction(function () use ($frames, $homeScore, $awayScore, $lock, $isOverridden) {
            $attributes = [
                'home_score' => $homeScore,
                'away_score' => $awayScore,
                'is_confirmed' => $lock,
                'is_overridden' => $isOverridden,
                'section_id' => $this->fixture->section_id,
                'ruleset_id' => $this->fixture->ruleset_id,
            ];

            if ($lock) {
                $attributes['submitted_by'] = auth()->id();
                $attributes['submitted_at'] = now();
            }

            if (! $this->result) {
                $this->result = Result::create(array_merge($attributes, [
                    'fixture_id' => $this->fixture->id,
                    'home_team_id' => $this->fixture->homeTeam->id,
                    'home_team_name' => $this->fixture->homeTeam->name,
                    'away_team_id' => $this->fixture->awayTeam->id,
                    'away_team_name' => $this->fixture->awayTeam->name,
                ]));
            } else {
                $this->result->update($attributes);
            }

            $this->syncPersistedFrames(array_values($frames));

            return $this->result->fresh(['frames' => fn ($query) => $query->orderBy('id')]);
        });
    }

    private function normalizePlayerId($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function normalizeScore($value): int
    {
        return (int) $value;
    }

    /**
     * @param  array<int, array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}>  $frames
     */
    private function syncPersistedFrames(array $frames): void
    {
        $existingFrames = $this->result
            ? $this->result->frames()->orderBy('id')->get()->values()
            : collect();

        foreach ($frames as $index => $frame) {
            $existingFrame = $existingFrames[$index] ?? null;

            if ($existingFrame instanceof Frame) {
                $existingFrame->update($frame);

                continue;
            }

            $this->result->frames()->create($frame);
        }

        $existingFrames
            ->slice(count($frames))
            ->each
            ->delete();
    }

    private function syncResultState(?Result $result): void
    {
        $this->result = $result;
        $this->isLocked = (bool) ($this->result?->is_confirmed ?? false);

        if (! $this->result) {
            $this->frames = $this->emptyFrames();
            $this->homeScore = 0;
            $this->awayScore = 0;
            $this->totalScore = 0;

            return;
        }

        $frames = $this->result->frames->sortBy('id')->values();

        $this->frames = $this->mapFramesToState($frames);
        $this->homeScore = (int) $this->result->home_score;
        $this->awayScore = (int) $this->result->away_score;
        $this->totalScore = $this->homeScore + $this->awayScore;
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

    private function isHomeOrAwayTeam(int $teamId): bool
    {
        return $this->fixture->homeTeam->id === $teamId || $this->fixture->awayTeam->id === $teamId;
    }

    private function recalculateScores(): void
    {
        $this->homeScore = array_sum(array_column($this->frames, 'home_score'));
        $this->awayScore = array_sum(array_column($this->frames, 'away_score'));
        $this->totalScore = $this->homeScore + $this->awayScore;
    }

    private function framesMatchExisting(array $frames): bool
    {
        if (! $this->result) {
            return empty($frames);
        }

        $existing = $this->result->relationLoaded('frames')
            ? $this->result->frames
            : $this->result->frames()->orderBy('id')->get();

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

    private function initializeLock(): void
    {
        if ($this->isLocked) {
            $this->clearLockState();

            return;
        }

        $user = auth()->user();

        $this->lock = FixtureResultLock::firstOrNew(['fixture_id' => $this->fixture->id]);

        if ($this->lock->exists && $this->lock->isActive() && $this->lock->locked_by !== $user->id) {
            $this->lock->loadMissing('user');
            $this->canEdit = false;
            $this->lockedByAnother = true;
            $this->lockOwnerName = $this->lock->user?->name ?? 'Another team admin';
            $this->lockExpiresAtHuman = optional($this->lock->locked_until)?->diffForHumans();

            return;
        }

        $this->lock->locked_by = $user->id;
        $this->lock->locked_until = now()->addMinutes($this->lockTimeoutMinutes);
        $this->lock->save();

        $this->canEdit = true;
        $this->lockedByAnother = false;
        $this->lockOwnerName = $user->name;
        $this->lockExpiresAtHuman = optional($this->lock->locked_until)?->diffForHumans();
    }

    private function clearLockState(): void
    {
        $this->lock = null;
        $this->canEdit = false;
        $this->lockedByAnother = false;
        $this->lockOwnerName = null;
        $this->lockExpiresAtHuman = null;
    }

    private function releaseLock(): void
    {
        if ($this->lock && $this->lock->locked_by === auth()->id()) {
            $this->lock->delete();
        }

        $this->clearLockState();
    }

    private function redirectToFixtureOrResult()
    {
        $result = $this->result ?? $this->fixture->result;

        if ($result) {
            return redirect()->route('result.show', $result);
        }

        return redirect()->route('fixture.show', $this->fixture);
    }

    private function prepareFrames(bool $requireComplete, bool $allowEmpty = false): array
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
                    $errors["frames.$i"] = 'Complete every frame before submitting.';
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
                'frames' => 'Add at least one completed frame before saving.',
            ]);
        }

        if (! empty($frames)) {
            $this->validateSharedRules($frames);
        }

        if ($requireComplete) {
            if (count($frames) !== 10) {
                throw ValidationException::withMessages([
                    'frames' => 'All 10 frames must be completed before submitting.',
                ]);
            }

            $this->validateLockSpecificRules($frames);
        }

        return $frames;
    }
}
