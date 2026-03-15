<?php

namespace App\Livewire;

use App\Models\Fixture;
use App\Models\Section;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
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
                'fixtures' => $this->fixtures,
            ]
        );
    }
}
