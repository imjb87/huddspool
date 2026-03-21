<?php

namespace App\Livewire;

use App\Livewire\Forms\FixtureResultForm;
use App\Models\Fixture;
use App\Models\FixtureResultLock;
use App\Models\Result;
use App\Support\ResultFormDraftStore;
use App\Support\ResultFormFixtureAccess;
use App\Support\ResultFormFrameRowBuilder;
use App\Support\ResultFormLockManager;
use App\Support\ResultFormPersister;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

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

    protected ?ResultFormDraftStore $draftStore = null;

    protected ?ResultFormLockManager $lockManager = null;

    protected ?ResultFormPersister $persister = null;

    protected ?ResultFormFixtureAccess $fixtureAccess = null;

    public function mount(Fixture $fixture): void
    {
        $this->fixture = $this->fixtureAccess()->load($fixture);
        $this->result = $this->fixtureAccess()->ensureSubmittable($this->fixture);
        $this->hydrateResultState();
        $this->initializeLock();
    }

    public function updated($propertyName, $value): void
    {
        if (str_starts_with($propertyName, 'form.frames.')) {
            $this->handleFrameUpdate();
        }
    }

    public function submit(): Redirector
    {
        $this->refreshActionState();

        if ($this->isLocked) {
            return redirect()->route('result.show', $this->result);
        }

        if (! $this->canEdit) {
            return $this->redirectToFixtureOrResult();
        }

        $frames = $this->form->prepareFrames(requireComplete: true);
        $result = $this->persister()->persist($this->fixture, $this->result, $frames, lock: true);

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

    public function render(): View
    {
        return view('livewire.result-form', [
            'frameRows' => $this->frameRows(),
        ]);
    }

    private function refreshActionState(): void
    {
        $this->fixture = $this->fixtureAccess()->load($this->fixture);
        $this->fixtureAccess()->ensureAccessible($this->fixture);

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
        $this->form->syncFromResultAndDraft(
            $this->result,
            $this->draftStore()->get($this->authenticatedUserId(), (int) $this->fixture->getKey()),
        );
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

        $result = $this->persister()->persist($this->fixture, $this->result, $frames, lock: false);

        $this->syncComponentState($result);
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

    private function initializeLock(): void
    {
        if ($this->isLocked) {
            $this->clearLockState();

            return;
        }

        $this->applyLockState(
            $this->lockManager()->acquire($this->fixture, auth()->user(), $this->lockTimeoutMinutes),
        );
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
        $this->lockManager()->release($this->lock, $this->authenticatedUserId());

        $this->clearLockState();
    }

    /**
     * @param  array{
     *     lock: ?FixtureResultLock,
     *     canEdit: bool,
     *     lockedByAnother: bool,
     *     lockOwnerName: ?string,
     *     lockExpiresAtHuman: ?string
     * }  $state
     */
    private function applyLockState(array $state): void
    {
        $this->lock = $state['lock'];
        $this->canEdit = $state['canEdit'];
        $this->lockedByAnother = $state['lockedByAnother'];
        $this->lockOwnerName = $state['lockOwnerName'];
        $this->lockExpiresAtHuman = $state['lockExpiresAtHuman'];
    }

    private function persistDraftFramesToSession(): void
    {
        $this->draftStore()->put($this->authenticatedUserId(), (int) $this->fixture->getKey(), $this->form->frames);
    }

    private function clearDraftFramesFromSession(): void
    {
        $this->draftStore()->forget($this->authenticatedUserId(), (int) $this->fixture->getKey());
    }

    private function redirectToFixtureOrResult(): Redirector
    {
        $result = $this->result ?? $this->fixture->result;

        if ($result) {
            return redirect()->route('result.show', $result);
        }

        return redirect()->route('fixture.show', $this->fixture);
    }

    private function draftStore(): ResultFormDraftStore
    {
        return $this->draftStore ??= new ResultFormDraftStore;
    }

    private function lockManager(): ResultFormLockManager
    {
        return $this->lockManager ??= new ResultFormLockManager;
    }

    private function persister(): ResultFormPersister
    {
        return $this->persister ??= new ResultFormPersister;
    }

    private function fixtureAccess(): ResultFormFixtureAccess
    {
        return $this->fixtureAccess ??= new ResultFormFixtureAccess;
    }

    private function frameRows(): array
    {
        return (new ResultFormFrameRowBuilder)->build($this->fixture, $this->form->frames);
    }

    private function authenticatedUserId(): int
    {
        return (int) auth()->id();
    }
}
