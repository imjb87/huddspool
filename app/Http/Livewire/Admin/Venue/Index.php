<?php

namespace App\Http\Livewire\Admin\Venue;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Venue;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.venue.index', [
            'venues' => Venue::where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%')
                        ->orderBy('name')
                        ->simplePaginate(10)
        ])->layout('layouts.admin');
    }
}
