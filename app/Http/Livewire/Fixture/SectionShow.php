<?php

namespace App\Http\Livewire\Fixture;

use Livewire\Component;
use App\Models\Section;
use App\Models\Fixture;
use Livewire\WithPagination;

class SectionShow extends Component
{
    use WithPagination;

    public Section $section;
    public $week = 1;

    public function mount(Section $section)
    {
        $this->section = $section;

        foreach ($section->season->dates as $key => $date) {
            if (date('W', strtotime($date)) == date('W')) {
                $this->week = $key + 1;
                break; // Exit the loop after setting the page.
            }
        }
    }

    public function previousWeek()
    {
        $this->week--;
    }

    public function nextWeek()
    {
        $this->week++;
    }

    public function render()
    {
        return view(
            'livewire.fixture.section-show',
            [
                'fixtures' => Fixture::where('section_id', $this->section->id)
                    ->where('week', $this->week)
                    ->get()
            ]
        );
    }
}
