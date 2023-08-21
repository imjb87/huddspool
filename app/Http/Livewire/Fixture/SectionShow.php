<?php

namespace App\Http\Livewire\Fixture;

use Livewire\Component;
use App\Models\Section;
use App\Models\Fixture;
use Illuminate\Support\Arr;
use Livewire\WithPagination;

class SectionShow extends Component
{
    use WithPagination;

    public Section $section;
    public $page = 1;

    public function mount(Section $section)
    {
        $this->section = $section;

        Arr::map($section->season->dates, function ($date, $key) use (&$page) {
            if( date('W', strtotime($date)) == date('W') ) {
                $this->page = $key + 1;
            }
        });
    }

    public function render()
    {
        return view('livewire.fixture.section-show', [
            'fixtures' => Fixture::where('section_id', $this->section->id)
                ->orderBy('week')
                ->simplePaginate(5, ['*'], 'page', $this->page)
        ]);

    }
}
