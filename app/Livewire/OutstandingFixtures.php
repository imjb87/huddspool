<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Fixture;

class OutstandingFixtures extends Component
{
    public $fixtures;

    public function mount()
    {
        $this->fixtures = Fixture::doesnthave('result')->where('fixture_date', '<=', now())->get();
    }

    public function render()
    {
        return view('livewire.outstanding-fixtures');
    }
}
