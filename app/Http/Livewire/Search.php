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

    public function search()
    {
        if (strlen($this->searchTerm) < 3) {
            return;
        }
        
        $players = \App\Models\User::where('name', 'like', '%' . $this->searchTerm . '%')->orWhereHas('team', function ($query) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        })->orderBy('name')->get();
        $teams = \App\Models\Team::where('name', 'like', '%' . $this->searchTerm . '%')->orderBy('name')->get();
        $venues = \App\Models\Venue::where('name', 'like', '%' . $this->searchTerm . '%')->orderBy('name')->get();

        $this->searchResults = [];

        if ($players->count() > 0) {
            $this->searchResults['players'] = $players;
        }

        if ($teams->count() > 0) {
            $this->searchResults['teams'] = $teams;
        }

        if ($venues->count() > 0) {
            $this->searchResults['venues'] = $venues;
        }
    }

    public function render()
    {
        return view('livewire.search');
    }
}
