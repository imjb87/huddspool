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
        $this->searchResults = [];

        if (strlen($this->searchTerm) < 3) {
            return;
        }

        $players = \App\Models\User::where(function ($query) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHas('team', function ($query) {
                      $query->where('name', 'like', '%' . $this->searchTerm . '%')
                      ->where('folded_at', null);
                  });
        })->whereHas('team.sections.season', function ($query) {
            $query->where('is_open', 1);
        })->orderBy('name')->get();
    
        $teams = \App\Models\Team::where('name', 'like', '%' . $this->searchTerm . '%')
        ->where('folded_at', null)
        ->whereHas('sections', function ($query) {
            $query->whereHas('season', function ($query) {
                $query->where('is_open', 1);
            });
        })
        ->orderBy('name')
        ->get();
    
        $venues = \App\Models\Venue::where('name', 'like', '%' . $this->searchTerm . '%')->orderBy('name')->get();

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
