<?php

namespace App\Http\Livewire\Fixture;

use Livewire\Component;
use App\Models\Section;
use App\Models\Fixture;

class SectionShow extends Component
{
    public Section $section;
    public $page = 1;

    public function mount(Section $section)
    {
        $this->section = $section;
    }

    public function render()
    {
        return view('livewire.fixture.section-show', [
            'fixtures' => Fixture::where('section_id', $this->section->id)
                ->orderBy('fixture_date')
                ->simplePaginate(5, ['*'], 'page', $this->page)
                ->withQueryString()
        ])->layout('layouts.admin');

    }
}
