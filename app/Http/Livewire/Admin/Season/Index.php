<?php

namespace App\Http\Livewire\Admin\Season;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Season;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.admin.season.index', [
            'seasons' => Season::simplePaginate(10)
        ])->layout('layouts.admin');
    }
}
