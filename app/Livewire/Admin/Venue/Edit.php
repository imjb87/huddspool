<?php

namespace App\Livewire\Admin\Venue;

use Livewire\Component;
use App\Models\Venue;

class Edit extends Component
{
    public Venue $venue;

    protected $rules = [
        'venue.name' => 'required|string',
        'venue.address' => 'required|string',
        'venue.telephone' => 'nullable|string'
    ];

    protected $messages = [
        'venue.name.required' => 'The venue name is required',
        'venue.address.required' => 'The venue address is required',
    ];

    public function mount(Venue $venue)
    {
        $this->venue = $venue;
    }

    public function save()
    {
        $this->validate();

        $this->venue->save();

        return redirect()->route('admin.venues.show', $this->venue);
    }

    public function render()
    {
        return view('livewire.admin.venue.edit')->layout('layouts.admin');
    }
}
