<?php

namespace App\Http\Livewire\Admin\Fixture;

use Livewire\Component;
use App\Models\Fixture;

class Edit extends Component
{
    public $fixture;
    public $fixture_date = '';

    protected $rules = [
        'fixture_date' => 'required|date',
    ];

    public function mount(Fixture $fixture)
    {
        $this->fixture = $fixture;
        $this->fixture_date = $this->fixture->fixture_date->format('Y-m-d');
    }

    public function save()
    {
        $this->validate();

        $this->fixture->fixture_date = $this->fixture_date;
        $this->fixture->save();

        return redirect()->route('admin.fixtures.show', $this->fixture);
    }

    public function render()
    {
        return view('livewire.admin.fixture.edit')->layout('layouts.admin');
    }
}
