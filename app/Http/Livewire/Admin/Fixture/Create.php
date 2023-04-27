<?php

namespace App\Http\Livewire\Admin\Fixture;

use Livewire\Component;
use App\Models\Section;
use App\Services\FixtureGenerator;
use App\Rules\NoFixtureClashes;

class Create extends Component
{
    public Section $section;
    public array $schedule = [];

    protected function rules()
    {
        return ['schedule' => [new NoFixtureClashes($this->schedule)]];
    }

    public function mount(Section $section)
    {
        $this->section = $section;
        $this->schedule = (new FixtureGenerator($section))->generate();

        $this->validate();
    }

    public function save()
    {
        foreach ($this->schedule as $fixtures) {
            foreach ($fixtures as $fixture) {
                $this->section->fixtures()->create($fixture);
            }
        }

        return redirect()->route('admin.sections.show', $this->section);
    }

    public function render()
    {
        return view('livewire.admin.fixture.create')->layout('layouts.admin');
    }
}
