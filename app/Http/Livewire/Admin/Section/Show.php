<?php

namespace App\Http\Livewire\Admin\Section;

use Livewire\Component;
use App\Models\Section;
use App\Models\Fixture;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public Section $section;

    public function delete()
    {
        $this->section->delete();

        return redirect()->route('admin.seasons.show', $this->section->season);
    }

    public function showFixture(Fixture $fixture)
    {
        return redirect()->route('admin.fixtures.show', $fixture);
    }

    public function regenerateFixtures()
    {
        $this->section->fixtures()->delete();

        return redirect()->route('admin.fixtures.create', $this->section);
    }

    public function render()
    {
        return view('livewire.admin.section.show', [
            'fixtures' => Fixture::where('section_id', $this->section->id)
                ->orderBy('fixture_date')
                ->simplePaginate(5)
        ])->layout('layouts.admin');
    }
}
