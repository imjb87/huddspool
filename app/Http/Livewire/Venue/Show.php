<?php

namespace App\Http\Livewire\Venue;

use Livewire\Component;
use App\Models\Venue;

class Show extends Component
{
    public Venue $venue;

    public function mount(Venue $venue)
    {
        $this->venue = $venue;
    }
    
    public function render()
    {
        return view('livewire.venue.show');
    }
}
