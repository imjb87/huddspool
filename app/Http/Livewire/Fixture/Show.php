<?php

namespace App\Http\Livewire\Fixture;

use Livewire\Component;
use App\Models\Fixture;

class Show extends Component
{
    public Fixture $fixture;
    public $isTeamAdmin = false;
    public $isAdmin = false;

    public function mount(Fixture $fixture)
    {
        $this->fixture = $fixture;

        if ($this->fixture->homeTeam->id == 1 || $this->fixture->awayTeam->id == 1) {
            return redirect()->route('home');
        }

        if ($this->fixture->result) {
            return redirect()->route('result.show', $this->fixture->result);
        }

        if (auth()->check()) {
            if ($this->fixture->homeTeam->id == auth()->user()->team_id || $this->fixture->awayTeam->id == auth()->user()->team_id) {
                if (auth()->user()->role == 2) {
                    $this->isTeamAdmin = true;
                }
            }
            if (auth()->user()->is_admin == 1) {
                $this->isAdmin = true;
            }
        }
    }

    public function render()
    {
        return view('livewire.fixture.show')->layout('layouts.app', ['title' => $this->fixture->homeTeam->name . ' vs ' . $this->fixture->awayTeam->name]);
    }
}
