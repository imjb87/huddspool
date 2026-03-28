<?php

namespace App\Livewire;

use App\Events\LeagueResultDraftUpdated;
use App\Events\LeagueResultSubmitted;
use App\Exceptions\StaleResultDraftException;
use App\Livewire\Forms\FixtureResultForm;
use App\Models\Fixture;
use App\Models\Result;
use App\Models\User;
use App\Support\ResultDraftPayloadFactory;
use App\Support\ResultFormFixtureAccess;
use App\Support\ResultFormFrameRowBuilder;
use App\Support\ResultFormPersister;
use Illuminate\Support\Str;
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
    public int $draftVersion = 0;

    #[Locked]
    public string $clientId;

    #[Locked]
    public ?string $lastUpdatedByName = null;

    /**
     * @var array<int, array{id: int, name: string, avatar_url: string}>
     */
    public array $collaborators = [];

    #[Locked]
    public ?string $lastEditedAt = null;

    protected ?ResultFormPersister $persister = null;

    protected ?ResultFormFixtureAccess $fixtureAccess = null;

    protected ?ResultDraftPayloadFactory $draftPayloadFactory = null;

    public function mount(Fixture $fixture): void
    {
        $this->clientId = (string) Str::uuid();
        $this->fixture = $this->fixtureAccess()->load($fixture);
        $this->fixtureAccess()->ensureAccessible($this->fixture);
        $this->collaborators = [$this->collaboratorDetails(auth()->user())];
        $this->hydrateResultState();
    }

    public function updated($propertyName, $value): void
    {
        if (str_starts_with($propertyName, 'form.frames.')) {
            $this->handleFrameUpdate();
        }
    }

    public function submit(): ?Redirector
    {
        $this->refreshActionState();

        if ($this->isLocked || ! $this->canEdit) {
            return redirect()->route('result.show', $this->result);
        }

        $frames = $this->form->prepareFrames(requireComplete: true);

        try {
            $result = $this->persister()->submit(
                fixture: $this->fixture,
                result: $this->result,
                draftFrames: $this->form->draftFrames(),
                completedFrames: $frames,
                updatedBy: $this->authenticatedUserId(),
                expectedDraftVersion: $this->draftVersion,
            );
        } catch (StaleResultDraftException $exception) {
            $this->handleStaleDraftException($exception, 'Another editor updated this result. We refreshed the latest draft for you.');

            if ($this->isLocked && $this->result) {
                return redirect()->route('result.show', $this->result);
            }

            return null;
        }

        $this->syncComponentState($result);

        event(new LeagueResultSubmitted($this->draftPayloadFactory()->make(
            fixture: $this->fixture,
            result: $result,
            clientId: $this->clientId,
        )));

        sleep(1);

        return redirect()->route('result.show', $this->result);
    }

    /**
     * @param  array<int|string, array{home_player_id?: int|string|null, away_player_id?: int|string|null, home_score?: int|string|null, away_score?: int|string|null}>  $frames
     */
    public function restoreClientDraft(array $frames, int $draftVersion): void
    {
        $this->refreshActionState();

        if ($this->isLocked || ! $this->canEdit || $draftVersion !== $this->draftVersion) {
            return;
        }

        $this->form->syncFromResultAndDraft($this->result, $frames);

        if ($this->form->matchesDraftState($this->result?->draft_state)) {
            return;
        }

        $this->persistCurrentDraft();
    }

    /**
     * @param  array<int|string, array{home_player_id?: int|string|null, away_player_id?: int|string|null, home_score?: int|string|null, away_score?: int|string|null}>  $frames
     */
    public function mergeClientDraft(array $frames, int $draftVersion): void
    {
        $this->refreshActionState();

        $currentDraftVersion = (int) ($this->result?->draft_version ?? 0);

        if ($this->isLocked || ! $this->canEdit || $draftVersion !== $currentDraftVersion) {
            return;
        }

        $this->draftVersion = $currentDraftVersion;

        $this->form->syncFromResultAndDraft($this->result, $frames);

        if ($this->form->matchesDraftState($this->result?->draft_state)) {
            return;
        }

        $this->persistCurrentDraft();
    }

    /**
     * @param  array{id: int|string, name?: string|null, avatar_url?: string|null}  $member
     */
    public function collaboratorJoined(array $member): void
    {
        $this->collaborators = collect($this->collaborators)
            ->push($this->mapCollaborator($member))
            ->unique('id')
            ->values()
            ->all();
    }

    /**
     * @param  array{id: int|string}  $member
     */
    public function collaboratorLeft(array $member): void
    {
        $memberId = (int) $member['id'];

        $this->collaborators = collect($this->collaborators)
            ->reject(fn (array $collaborator): bool => $collaborator['id'] === $memberId)
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array{id: int|string, name?: string|null, avatar_url?: string|null}>  $members
     */
    public function syncCollaborators(array $members): void
    {
        $this->collaborators = collect($members)
            ->map(fn (array $member): array => $this->mapCollaborator($member))
            ->unique('id')
            ->values()
            ->all();
    }

    /**
     * @param  array{
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
     * }  $payload
     */
    public function syncDraftFromBroadcast(array $payload): void
    {
        if ((int) $payload['fixture_id'] !== (int) $this->fixture->getKey()) {
            return;
        }

        $changedFrameNumbers = $this->changedFrameNumbersFromPayload($payload['frames'] ?? []);

        $this->fixture = $this->fixtureAccess()->load($this->fixture);

        if ($this->fixture->result) {
            $this->syncComponentState($this->fixture->result);
        } else {
            $this->result = null;
            $this->isLocked = (bool) ($payload['is_confirmed'] ?? false);
            $this->canEdit = ! $this->isLocked;
            $this->draftVersion = (int) ($payload['draft_version'] ?? 0);
            $this->lastUpdatedByName = $payload['updated_by_name'] ?? null;
            $this->form->syncFromResultAndDraft(null, $payload['frames'] ?? []);
        }

        if ($changedFrameNumbers !== []) {
            $this->dispatch('result-frames-synced', frameNumbers: $changedFrameNumbers);
        }
    }

    public function refreshSharedDraft(): void
    {
        $this->fixture = $this->fixtureAccess()->load($this->fixture);
        $this->fixtureAccess()->ensureAccessible($this->fixture);

        $changedFrameNumbers = $this->changedFrameNumbersFromPayload($this->fixture->result?->draft_state ?? []);

        $this->syncComponentState($this->fixture->result);

        if ($changedFrameNumbers !== []) {
            $this->dispatch('result-frames-synced', frameNumbers: $changedFrameNumbers);
        }
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
        $this->canEdit = ! $this->isLocked;
    }

    private function hydrateResultState(): void
    {
        $this->syncComponentState($this->fixture->result);
    }

    private function syncComponentState(?Result $result): void
    {
        $this->result = $result?->load([
            'frames' => fn ($query) => $query->orderBy('id'),
            'draftUpdatedBy',
        ]);
        $this->isLocked = (bool) ($this->result?->is_confirmed ?? false);
        $this->canEdit = ! $this->isLocked;
        $this->draftVersion = (int) ($this->result?->draft_version ?? 0);
        $this->lastUpdatedByName = $this->result?->draftUpdatedBy?->name;
        $this->lastEditedAt = $this->result?->updated_at?->format('D j M Y \\a\\t H:i');
        $this->form->syncFromResultAndDraft(
            $this->result,
            $this->result?->draft_state,
        );

        $this->dispatch('result-form-server-synced', [
            'draftVersion' => $this->draftVersion,
            'frames' => $this->form->draftFrames(),
            'isLocked' => $this->isLocked,
        ]);
    }

    public function handleFrameUpdate(): void
    {
        $this->refreshActionState();

        if ($this->isLocked || ! $this->canEdit) {
            return;
        }

        if ($this->result && $this->draftVersion !== (int) $this->result->draft_version) {
            $this->syncComponentState($this->result);

            return;
        }

        if ($this->result === null && $this->form->draftFramesAreEmpty()) {
            return;
        }

        if ($this->form->matchesDraftState($this->result?->draft_state)) {
            return;
        }

        $this->persistCurrentDraft();
    }

    private function redirectToFixtureOrResult(): Redirector
    {
        $result = $this->result ?? $this->fixture->result;

        if ($result) {
            return redirect()->route('result.show', $result);
        }

        return redirect()->route('fixture.show', $this->fixture);
    }

    private function persister(): ResultFormPersister
    {
        return $this->persister ??= new ResultFormPersister;
    }

    private function fixtureAccess(): ResultFormFixtureAccess
    {
        return $this->fixtureAccess ??= new ResultFormFixtureAccess;
    }

    private function draftPayloadFactory(): ResultDraftPayloadFactory
    {
        return $this->draftPayloadFactory ??= new ResultDraftPayloadFactory;
    }

    private function frameRows(): array
    {
        return (new ResultFormFrameRowBuilder)->build($this->fixture, $this->form->frames);
    }

    private function authenticatedUserId(): int
    {
        return (int) auth()->id();
    }

    /**
     * @param  array<int|string, array{home_player_id?: int|string|null, away_player_id?: int|string|null, home_score?: int|string|null, away_score?: int|string|null}>  $incomingFrames
     * @return array<int, int>
     */
    private function changedFrameNumbersFromPayload(array $incomingFrames): array
    {
        $currentFrames = $this->form->draftFrames();
        $changedFrameNumbers = [];

        for ($i = 1; $i <= 10; $i++) {
            $incomingFrame = $incomingFrames[$i] ?? $incomingFrames[(string) $i] ?? [];
            $currentFrame = $currentFrames[$i] ?? [];

            if (
                $this->normalizeFrameValue($currentFrame['home_player_id'] ?? null) !== $this->normalizeFrameValue($incomingFrame['home_player_id'] ?? null) ||
                $this->normalizeFrameValue($currentFrame['away_player_id'] ?? null) !== $this->normalizeFrameValue($incomingFrame['away_player_id'] ?? null) ||
                $this->normalizeFrameValue($currentFrame['home_score'] ?? 0) !== $this->normalizeFrameValue($incomingFrame['home_score'] ?? 0) ||
                $this->normalizeFrameValue($currentFrame['away_score'] ?? 0) !== $this->normalizeFrameValue($incomingFrame['away_score'] ?? 0)
            ) {
                $changedFrameNumbers[] = $i;
            }
        }

        return $changedFrameNumbers;
    }

    private function normalizeFrameValue(mixed $value): int|string|null
    {
        if ($value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return $value;
    }

    private function persistCurrentDraft(): void
    {
        try {
            $result = $this->persister()->persistDraft(
                fixture: $this->fixture,
                result: $this->result,
                draftFrames: $this->form->draftFrames(),
                updatedBy: $this->authenticatedUserId(),
                expectedDraftVersion: $this->draftVersion,
            );
        } catch (StaleResultDraftException $exception) {
            $this->handleStaleDraftException($exception, 'Another editor updated this result before your latest changes were saved.');

            return;
        }

        $this->syncComponentState($result);
        $this->resetErrorBag('form.frames');

        event(new LeagueResultDraftUpdated($this->draftPayloadFactory()->make(
            fixture: $this->fixture,
            result: $result,
            clientId: $this->clientId,
        )));
    }

    private function handleStaleDraftException(StaleResultDraftException $exception, string $message): void
    {
        $this->fixture = $this->fixtureAccess()->load($this->fixture);
        $this->syncComponentState($exception->result);
        $this->addError('form.frames', $message);
    }

    /**
     * @param  array{id: int|string, name?: string|null, avatar_url?: string|null}  $member
     * @return array{id: int, name: string, avatar_url: string}
     */
    private function mapCollaborator(array $member): array
    {
        $user = User::query()->find((int) $member['id']);

        return [
            'id' => (int) $member['id'],
            'name' => (string) ($member['name'] ?? $user?->name ?? 'Team admin'),
            'avatar_url' => (string) ($member['avatar_url'] ?? $user?->avatar_url ?? asset('/images/user.jpg')),
        ];
    }

    /**
     * @return array{id: int, name: string, avatar_url: string}
     */
    private function collaboratorDetails(User $user): array
    {
        return [
            'id' => (int) $user->getKey(),
            'name' => $user->name,
            'avatar_url' => $user->avatar_url,
        ];
    }

    public function broadcastChannelName(): string
    {
        return 'fixture-results.'.$this->fixture->getKey();
    }
}
