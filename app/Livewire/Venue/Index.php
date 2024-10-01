<?php

namespace App\Livewire\Venue;

use Livewire\Component;
use App\Models\Venue;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        return view('livewire.venue.index', [
            'venues' => Venue::where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%')
                        ->orderByRaw('REPLACE(name, "The ", "")')
                        ->simplePaginate(10)
        ])->layout('layouts.app');
    }
}
