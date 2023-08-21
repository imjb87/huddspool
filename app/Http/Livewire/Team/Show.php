<?php

namespace App\Http\Livewire\Team;

use Livewire\Component;
use App\Models\Team;

class Show extends Component
{
    public Team $team;
    public $fixtures;

    public function mount(Team $team)
    {
        if( $team->id == 1 ) {
            abort(404);
        }

        $this->team = $team;
    }

    public function render()
    {
        return view('livewire.team.show')
            ->layout('layouts.app', ['title' => $this->team->name]);
    }
}
