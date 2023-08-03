<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Search extends Component
{
    public $isOpen = false;
    public $searchTerm;
    public $searchResults = [];

    protected $listeners = ['openSearch'];

    public function openSearch()
    {
        $this->isOpen = true;
        $this->searchTerm = '';
        $this->searchResults = [];
    }

    public function closeSearch()
    {
        $this->isOpen = false;
        $this->searchTerm = '';
        $this->searchResults = [];
    }

    public function updatedSearchTerm()
    {
        $this->searchResults = [];
        if (strlen($this->searchTerm) >= 3) {
            $this->searchResults['players'] = \App\Models\User::where('name', 'like', '%' . $this->searchTerm . '%')->orWhereHas('team', function ($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%');
            })->get();
            $this->searchResults['teams'] = \App\Models\Team::where('name', 'like', '%' . $this->searchTerm . '%')->get();
            $this->searchResults['venues'] = \App\Models\Venue::where('name', 'like', '%' . $this->searchTerm . '%')->get();
        }
    }

    public function render()
    {
        return view('livewire.search');
    }
}
