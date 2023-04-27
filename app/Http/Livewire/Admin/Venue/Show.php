<?php

namespace App\Http\Livewire\Admin\Venue;

use Livewire\Component;
use App\Models\Venue;

class Show extends Component
{
    public Venue $venue;

    public function mount(Venue $venue)
    {
        $this->venue = $venue;
    }

    public function delete()
    {
        $this->venue->delete();

        return redirect()->route('admin.venues.index');
    }

    public function render()
    {
        return view('livewire.admin.venue.show')->layout('layouts.admin');
    }
}
