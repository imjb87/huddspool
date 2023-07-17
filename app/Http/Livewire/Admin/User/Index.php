<?php

namespace App\Http\Livewire\Admin\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

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
        // order by role, then by team, then by name
        return view('livewire.admin.user.index', [
            'users' => User::where('name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('team', function ($query) {
                            $query->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orderBy('name')
                        ->simplePaginate(10)
        ])->layout('layouts.admin');
    }
}
