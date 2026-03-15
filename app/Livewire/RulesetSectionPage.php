<?php

namespace App\Livewire;

use App\Models\Fixture;
use App\Models\Ruleset;
use App\Models\Section;
use App\Queries\GetSectionAverages;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class RulesetSectionPage extends Component
{
    public Ruleset $ruleset;

    public Section $section;

    #[Url(as: 'tab', except: 'tables', history: true)]
    public string $activeTab = 'tables';

    #[Url(except: 1, history: true)]
    public int $week = 1;

    #[Url(except: 1, history: true)]
    public int $page = 1;

    public int $perPage = 10;

    /**
     * @var array<int, string>
     */
    private const ALLOWED_TABS = [
        'tables',
        'fixtures-results',
        'averages',
    ];

    public function mount(Ruleset $ruleset, Section $section, string $initialTab = 'tables'): void
    {
        $this->ruleset = $ruleset;
        $this->section = $section;
        $this->activeTab = $this->resolveActiveTab($initialTab);
        $this->page = max(1, $this->page);
        $this->week = request()->filled('week')
            ? max(1, $this->week)
            : $this->defaultWeek();
    }

    public function setActiveTab(string $tab): void
    {
        $tab = $this->resolveActiveTab($tab);

        if ($tab === $this->activeTab) {
            return;
        }

        $this->activeTab = $tab;

        if ($tab === 'fixtures-results') {
            $this->week = $this->defaultWeek();
        }

        $this->resetComputedState();
    }

    public function updatedPage(): void
    {
        $this->page = max(1, $this->page);

        unset($this->players);
    }

    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;

            unset($this->players);
        }
    }

    public function nextPage(): void
    {
        if ($this->players->count() === $this->perPage) {
            $this->page++;

            unset($this->players);
        }
    }

    public function updatedWeek(): void
    {
        $this->week = max(1, $this->week);

        unset($this->fixtures);
    }

    public function previousWeek(): void
    {
        $this->week = max(1, $this->week - 1);

        unset($this->fixtures);
    }

    public function nextWeek(): void
    {
        $this->week++;

        unset($this->fixtures);
    }

    public function tabUrl(string $tab): string
    {
        $parameters = [
            'ruleset' => $this->ruleset,
            'section' => $this->section,
        ];

        if ($tab !== 'tables') {
            $parameters['tab'] = $tab;
        }

        if ($tab === 'fixtures-results') {
            $fixturesWeek = $this->activeTab === 'fixtures-results'
                ? $this->week
                : $this->defaultWeek();

            if ($fixturesWeek !== 1) {
                $parameters['week'] = $fixturesWeek;
            }
        }

        if ($tab === 'averages' && $this->page !== 1) {
            $parameters['page'] = $this->page;
        }

        return route('ruleset.section.show', $parameters);
    }

    /**
     * @return array<string, string>
     */
    public function tabs(): array
    {
        return [
            'tables' => 'Tables',
            'fixtures-results' => 'Fixtures & Results',
            'averages' => 'Averages',
        ];
    }

    #[Computed]
    public function standingsSection(): Section
    {
        $this->section->loadMissing([
            'results' => fn ($query) => $query->where('is_confirmed', true),
            'season' => fn ($query) => $query->with('expulsions'),
            'teams' => fn ($query) => $query->withTrashed()->withPivot(['sort', 'section_id', 'team_id', 'deducted', 'withdrawn_at']),
        ]);

        return $this->section;
    }

    #[Computed]
    public function standings(): Collection
    {
        return $this->standingsSection->standings();
    }

    #[Computed]
    public function fixtures(): EloquentCollection
    {
        return Fixture::query()
            ->with(['result', 'homeTeam', 'awayTeam'])
            ->where('section_id', $this->section->id)
            ->where('week', $this->week)
            ->orderBy('fixture_date')
            ->get();
    }

    #[Computed]
    public function players(): Collection
    {
        return (new GetSectionAverages($this->section, $this->page, $this->perPage))();
    }

    public function render(): View
    {
        return view('livewire.ruleset-section-page');
    }

    private function resolveActiveTab(string $tab): string
    {
        return in_array($tab, self::ALLOWED_TABS, true) ? $tab : 'tables';
    }

    private function defaultWeek(): int
    {
        $currentDate = now();

        foreach (collect($this->section->season->dates ?? [])->flatten()->values() as $key => $date) {
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

    private function resetComputedState(): void
    {
        unset($this->standingsSection);
        unset($this->standings);
        unset($this->fixtures);
        unset($this->players);
    }
}
