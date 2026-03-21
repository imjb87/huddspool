<?php

namespace App\Support;

use App\Livewire\Forms\FixtureResultForm;
use App\Models\Result;

class ResultFormDraftSynchronizer
{
    public function __construct(
        private readonly ResultFormDraftStore $draftStore = new ResultFormDraftStore,
    ) {}

    public function syncForm(FixtureResultForm $form, ?Result $result, int $userId, int $fixtureId): void
    {
        $form->syncFromResultAndDraft(
            $result?->load(['frames' => fn ($query) => $query->orderBy('id')]),
            $this->draftStore->get($userId, $fixtureId),
        );
    }

    public function persist(array $frames, int $userId, int $fixtureId): void
    {
        $this->draftStore->put($userId, $fixtureId, $frames);
    }

    public function clear(int $userId, int $fixtureId): void
    {
        $this->draftStore->forget($userId, $fixtureId);
    }
}
