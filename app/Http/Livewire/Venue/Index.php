<?php

namespace App\Http\Livewire\Venue;

use Livewire\Component;
use App\Models\Venue;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.venue.index', [
            'venues' => Venue::orderBy('name')->simplePaginate(10)
        ])->layout('layouts.app');
    }
}
