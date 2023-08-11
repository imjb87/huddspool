<?php

namespace App\Http\Livewire\Fixture;

use Livewire\Component;
use App\Models\Fixture;

class Show extends Component
{
    public Fixture $fixture;
    public $isTeamAdmin = false;

    public function mount(Fixture $fixture)
    {
        $this->fixture = $fixture;

        if( $this->fixture->homeTeam->id == 1 || $this->fixture->awayTeam->id == 1 ) {
            abort(404);
        }

        if( $this->fixture->result ) {
            abort(404);
        }

        if( auth()->check() ) {
            if( $this->fixture->homeTeam->id == auth()->user()->team_id || $this->fixture->awayTeam->id == auth()->user()->team_id ) {
                if( auth()->user()->role == 2 ) {
                    $this->isTeamAdmin = true;
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.fixture.show');
    }
}
