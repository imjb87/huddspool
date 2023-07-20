<?php

namespace App\Http\Livewire\Ruleset;

use Livewire\Component;
use App\Models\Ruleset;

class Show extends Component
{
    public Ruleset $ruleset;

    public function mount(Ruleset $ruleset)
    {
        $this->ruleset = $ruleset;
    }
    
    public function render()
    {
        return view('livewire.ruleset.show');
    }
}
