<?php

namespace App\Livewire;

use App\Models\Ruleset;
use App\Models\Section;
use App\Queries\GetSectionAverages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

abstract class BaseSectionPage extends Component
{
    public Ruleset $ruleset;

    public Section $section;

    #[Url(as: 'tab', except: 'tables', history: true)]
    public string $activeTab = 'tables';

    #[Url(except: 1, history: true)]
    public int $week = 1;

    #[Url(except: 1, history: true)]
    public int $page = 1;

    public int $perPage = 5;

    /**
     * @var array<int, string>
     */
    private const ALLOWED_TABS = [
        'tables',
        'fixtures-results',
        'averages',
    ];

    protected function initializeSectionPage(Ruleset $ruleset, Section $section, string $initialTab = 'tables'): void
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
        $this->page = min(max(1, $this->page), $this->lastPage());

        unset($this->players);
        unset($this->totalPlayers);
    }

    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;

            unset($this->players);
            unset($this->totalPlayers);
        }
    }

    public function nextPage(): void
    {
        if ($this->hasNextPage()) {
            $this->page++;

            unset($this->players);
            unset($this->totalPlayers);
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
        $parameters = array_merge($this->baseRouteParameters(), [
            'section' => $this->section,
        ]);

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

        return route($this->routeName(), $parameters);
    }

    public function sectionUrl(Section $section): string
    {
        $parameters = array_merge($this->baseRouteParameters(), [
            'section' => $section,
        ]);

        if ($this->activeTab !== 'tables') {
            $parameters['tab'] = $this->activeTab;
        }

        return route($this->routeName(), $parameters);
    }

    /**
     * @return array<string, string>
     */
    public function tabs(): array
    {
        return [
            'tables' => 'Standings',
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
        return $this->fixturesQuery()
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

    #[Computed]
    public function totalPlayers(): int
    {
        return (new GetSectionAverages($this->section, 1, $this->perPage))->total();
    }

    private function resolveActiveTab(string $tab): string
    {
        return in_array($tab, self::ALLOWED_TABS, true) ? $tab : 'tables';
    }

    protected function resetComputedState(): void
    {
        unset($this->standingsSection);
        unset($this->standings);
        unset($this->fixtures);
        unset($this->players);
        unset($this->totalPlayers);
        unset($this->relatedSections);
    }

    protected function hasNextPage(): bool
    {
        return $this->page < $this->lastPage();
    }

    protected function lastPage(): int
    {
        return max(1, (int) ceil($this->totalPlayers / $this->perPage));
    }

    /**
     * @return array<string, mixed>
     */
    abstract protected function baseRouteParameters(): array;

    abstract protected function routeName(): string;

    abstract protected function fixturesQuery(): Builder;

    abstract protected function defaultWeek(): int;
}
