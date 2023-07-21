<?php

namespace App\Http\Livewire\Admin\Fixture;

use Livewire\Component;
use App\Models\Fixture;

class Edit extends Component
{
    public $fixture;

    public function mount(Fixture $fixture)
    {
        $this->fixture = $fixture;
    }

    public function render()
    {
        return view('livewire.admin.fixture.edit')->layout('layouts.admin');
    }
}
