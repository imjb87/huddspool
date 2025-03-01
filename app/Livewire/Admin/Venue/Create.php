<?php

namespace App\Livewire\Admin\Venue;

use Livewire\Component;
use App\Models\Venue;

class Create extends Component
{
    public $venue;

    protected $rules = [
        'venue.name' => 'required|string',
        'venue.address' => 'required|string',
        'venue.telephone' => 'nullable|string',
    ];

    protected $messages = [
        'venue.name.required' => 'The venue name is required',
        'venue.address.required' => 'The venue address is required',
    ];

    public function save()
    {
        $this->validate();

        $venue = Venue::create([
            'name' => $this->venue['name'],
            'address' => $this->venue['address'],
            'telephone' => $this->venue['telephone'],
        ]);
        
        return redirect()->route('admin.venues.show', $venue);
    }

    public function render()
    {
        return view('livewire.admin.venue.create')->layout('layouts.admin');
    }
}
