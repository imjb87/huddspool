<?php

namespace App\Livewire\Admin\Team;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Team;

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
        return view('livewire.admin.team.index', [
            'teams' => Team::where('name', 'like', '%' . $this->search . '%')
                        ->orderBy('name')
                        ->simplePaginate(10)
        ])->layout('layouts.admin');
    }
}
