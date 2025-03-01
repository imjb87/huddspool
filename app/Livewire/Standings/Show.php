<?php

namespace App\Livewire\Standings;

use Livewire\Component;

class Show extends Component
{
    public $section;
    public $standings;

    public function mount($section)
    {
        $this->section = $section;

        $this->standings = $this->section->teams->sortByDesc(function ($team) {
            return $team->pivot->points;
        });
    }

    public function render()
    {
        return view('livewire.standings.show');
    }
}
