<?php

namespace App\Livewire\History;

use App\Livewire\BaseSectionPage;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;

class SectionPage extends BaseSectionPage
{
    public Season $season;

    public function mount(Season $season, Ruleset $ruleset, Section $section, string $initialTab = 'tables'): void
    {
        $this->season = $season;
        $this->initializeSectionPage($ruleset, $section, $initialTab);
    }

    #[Computed]
    public function relatedSections(): EloquentCollection
    {
        return Section::query()
            ->where('season_id', $this->season->id)
            ->where('ruleset_id', $this->ruleset->id)
            ->orderBy('name')
            ->get()
            ->reject(fn (Section $section) => $section->is($this->section))
            ->values();
    }

    public function render(): View
    {
        return view('livewire.history.section-page', $this->sectionPageViewData(true));
    }

    protected function baseRouteParameters(): array
    {
        return [
            'season' => $this->season,
            'ruleset' => $this->ruleset,
        ];
    }

    protected function routeName(): string
    {
        return 'history.section.show';
    }

    protected function fixturesQuery(): Builder
    {
        return $this->season->fixtures()->getQuery();
    }

    protected function defaultWeek(): int
    {
        $currentDate = $this->season->lastScheduledDate() ?? now();

        foreach (collect($this->season->dates ?? [])->flatten()->values() as $key => $date) {
            try {
                $seasonWeekDate = Carbon::parse($date);
            } catch (\Throwable) {
                continue;
            }

            if ($seasonWeekDate->isoWeek() === $currentDate->isoWeek()
                && $seasonWeekDate->isoWeekYear() === $currentDate->isoWeekYear()) {
                return $key + 1;
            }
        }

        return 1;
    }
}
