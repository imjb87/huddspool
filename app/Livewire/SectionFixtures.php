<?php

namespace App\Livewire;

use App\Models\Fixture;
use App\Models\Section;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class SectionFixtures extends Component
{
    public Section $section;

    #[Url(except: 1, history: true)]
    public int $week = 1;

    public function mount(Section $section): void
    {
        $this->section = $section;

        if (request()->filled('week')) {
            $this->week = max(1, request()->integer('week'));

            return;
        }

        foreach ($section->season->dates as $key => $date) {
            if (date('W', strtotime($date)) == date('W')) {
                $this->week = $key + 1;
                break; // Exit the loop after setting the page.
            }
        }
    }

    #[Computed]
    public function fixtures(): Collection
    {
        return Fixture::query()
            ->with(['result', 'homeTeam', 'awayTeam'])
            ->where('section_id', $this->section->id)
            ->where('week', $this->week)
            ->orderBy('fixture_date')
            ->get();
    }

    public function previousWeek(): void
    {
        $this->week = max(1, $this->week - 1);
    }

    public function nextWeek(): void
    {
        $this->week++;
    }

    public function render(): View
    {
        return view(
            'livewire.section-fixtures',
            [
                'history' => false,
                'fixtures' => $this->fixtures,
                'fixtureRows' => $this->fixtureRows(),
            ]
        );
    }

    /**
     * @return SupportCollection<int, object>
     */
    public function fixtureRows(): SupportCollection
    {
        $isHistoryView = false;

        return $this->fixtures->map(function (Fixture $fixture) use ($isHistoryView) {
            $isByeFixture = $fixture->isBye();
            $fixtureLink = $fixture->result
                ? route('result.show', $fixture->result)
                : (! $isHistoryView ? route('fixture.show', $fixture) : null);
            $homeDisplayName = $isHistoryView && $fixture->result?->home_team_name
                ? $fixture->result->home_team_name
                : $fixture->homeTeam->name;
            $awayDisplayName = $isHistoryView && $fixture->result?->away_team_name
                ? $fixture->result->away_team_name
                : $fixture->awayTeam->name;

            return (object) [
                'fixture' => $fixture,
                'is_bye' => $isByeFixture,
                'link' => $fixtureLink,
                'home_team_name' => $fixture->homeTeam->shortname && ! $isHistoryView ? $fixture->homeTeam->shortname : $homeDisplayName,
                'away_team_name' => $fixture->awayTeam->shortname && ! $isHistoryView ? $fixture->awayTeam->shortname : $awayDisplayName,
                'row_meta' => $fixture->fixture_date->format('j M Y'),
                'row_classes' => 'block rounded-lg',
            ];
        });
    }
}
