<?php

namespace App\Livewire;

use App\Livewire\Forms\FixtureResultForm;
use App\Models\Fixture;
use App\Models\FixtureResultLock;
use App\Models\Frame;
use App\Models\Result;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ResultForm extends Component
{
    public Fixture $fixture;

    public FixtureResultForm $form;

    public ?Result $result = null;

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

    public function updated($propertyName, $value): void
    {
        if (str_starts_with($propertyName, 'form.frames.')) {
            $this->handleFrameUpdate();
        }
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

        $frames = $this->form->prepareFrames(requireComplete: true);
        $result = $this->persistFrames($frames, lock: true);

        $this->syncComponentState($result);
        $this->clearDraftFramesFromSession();
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
        $this->syncComponentState($this->fixture->result);
    }

    private function syncComponentState(?Result $result): void
    {
        $this->result = $result?->load(['frames' => fn ($query) => $query->orderBy('id')]);
        $this->isLocked = (bool) ($this->result?->is_confirmed ?? false);
        $this->form->syncFromResultAndDraft($this->result, $this->draftFramesFromSession());
    }

    public function handleFrameUpdate(): void
    {
        $this->refreshActionState();

        if ($this->isLocked || ! $this->canEdit) {
            return;
        }

        $this->persistDraftFramesToSession();

        try {
            $frames = $this->form->prepareFrames(requireComplete: false, allowEmpty: true);
        } catch (ValidationException $exception) {
            throw $exception;
        }

        if ($this->result === null && empty($frames)) {
            return;
        }

        if ($this->shouldSkipAutosaveForIncompleteEdit($frames)) {
            return;
        }

        if ($this->form->matchesExistingFrames($this->result, $frames)) {
            return;
        }

        $result = $this->persistFrames($frames, lock: false);

        $this->syncComponentState($result);
    }

    private function persistFrames(array $frames, bool $lock): Result
    {
        $isOverridden = $this->result?->is_overridden ?? 0;
        $scores = $this->scoresFromFrames($frames);

        return DB::transaction(function () use ($frames, $lock, $isOverridden, $scores) {
            $attributes = [
                'home_score' => $scores['home_score'],
                'away_score' => $scores['away_score'],
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

    /**
     * @param  array<int, array{home_player_id: ?int, away_player_id: ?int, home_score: int, away_score: int}>  $frames
     */
    private function shouldSkipAutosaveForIncompleteEdit(array $frames): bool
    {
        if (! $this->result) {
            return false;
        }

        $existingFrameCount = $this->result->relationLoaded('frames')
            ? $this->result->frames->count()
            : $this->result->frames()->count();

        return count($frames) < $existingFrameCount;
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

    private function isHomeOrAwayTeam(int $teamId): bool
    {
        return $this->fixture->homeTeam->id === $teamId || $this->fixture->awayTeam->id === $teamId;
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

    /**
     * @return array<int, array{home_player_id?: int|string|null, away_player_id?: int|string|null, home_score?: int|string|null, away_score?: int|string|null}>|null
     */
    private function draftFramesFromSession(): ?array
    {
        $draftFrames = session($this->draftFramesSessionKey());

        return is_array($draftFrames) ? $draftFrames : null;
    }

    private function persistDraftFramesToSession(): void
    {
        if ($this->draftFramesAreEmpty()) {
            $this->clearDraftFramesFromSession();

            return;
        }

        session()->put($this->draftFramesSessionKey(), $this->form->frames);
    }

    private function clearDraftFramesFromSession(): void
    {
        session()->forget($this->draftFramesSessionKey());
    }

    private function draftFramesAreEmpty(): bool
    {
        foreach ($this->form->frames as $frame) {
            $homePlayerId = $frame['home_player_id'] ?? null;
            $awayPlayerId = $frame['away_player_id'] ?? null;
            $homeScore = (int) ($frame['home_score'] ?? 0);
            $awayScore = (int) ($frame['away_score'] ?? 0);

            if ($homePlayerId !== null && $homePlayerId !== '') {
                return false;
            }

            if ($awayPlayerId !== null && $awayPlayerId !== '') {
                return false;
            }

            if ($homeScore > 0 || $awayScore > 0) {
                return false;
            }
        }

        return true;
    }

    private function draftFramesSessionKey(): string
    {
        return 'result-form-draft:'.auth()->id().':'.$this->fixture->getKey();
    }

    private function redirectToFixtureOrResult()
    {
        $result = $this->result ?? $this->fixture->result;

        if ($result) {
            return redirect()->route('result.show', $result);
        }

        return redirect()->route('fixture.show', $this->fixture);
    }
}
