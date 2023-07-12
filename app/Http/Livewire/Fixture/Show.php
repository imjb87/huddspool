<?php

namespace App\Http\Livewire\Fixture;

use Livewire\Component;
use App\Models\Fixture;

class Show extends Component
{
    public Fixture $fixture;
    public $isCaptain = false;

    public function mount(Fixture $fixture)
    {
        $this->fixture = $fixture;

        if( $this->fixture->homeTeam->id == 1 || $this->fixture->awayTeam->id == 1 ) {
            abort(404);
        }

        if( auth()->check() ) {
            $user_id = auth()->user()->id;
            $this->isCaptain = $this->fixture->homeTeam->captain->id == $user_id || $this->fixture->awayTeam->captain->id == $user_id;
        }
    }

    public function render()
    {
        return view('livewire.fixture.show');
    }
}
