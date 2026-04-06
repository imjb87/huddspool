<?php

namespace App\Livewire;

use App\Models\Ruleset;
use App\Models\Section;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Livewire\Attributes\Computed;

class RulesetSectionPage extends BaseSectionPage
{
    public function mount(Ruleset $ruleset, Section $section, string $initialTab = 'tables'): void
    {
        $this->initializeSectionPage($ruleset, $section, $initialTab);
    }

    #[Computed]
    public function relatedSections(): EloquentCollection
    {
        $this->ruleset->loadMissing('openSections.season');

        return $this->ruleset->openSections
            ->reject(fn (Section $section) => $section->is($this->section))
            ->values();
    }

    public function render(): View
    {
        return view('livewire.ruleset-section-page', $this->sectionPageViewData(false));
    }

    protected function baseRouteParameters(): array
    {
        return [
            'ruleset' => $this->ruleset,
        ];
    }

    protected function routeName(): string
    {
        return 'ruleset.section.show';
    }

    protected function fixturesQuery(): Builder
    {
        return Section::query()->findOrFail($this->section->id)->fixtures()->getQuery();
    }

    protected function defaultWeek(): int
    {
        return $this->section->season->currentOrPreviousScheduledWeek();
    }
}
