<?php

namespace App\Livewire\Admin\Fixture;

use Livewire\Component;
use App\Models\Fixture;

class Show extends Component
{
    public Fixture $fixture;

    public function deleteFixture()
    {
        $this->fixture->delete();

        return redirect()->route('admin.sections.show', $this->fixture->section);
    }

    public function deleteResult()
    {
        $this->fixture->result->delete();

        return redirect()->route('admin.fixtures.show', $this->fixture);
    }

    public function render()
    {
        return view('livewire.admin.fixture.show')->layout('layouts.admin');
    }
}
