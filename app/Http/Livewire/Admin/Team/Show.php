<?php

namespace App\Http\Livewire\Admin\Team;

use Livewire\Component;
use App\Models\Team;

class Show extends Component
{
    public Team $team;

    public function mount(Team $team)
    {
        $this->team = $team;
    }

    public function delete()
    {
        $this->team->delete();

        return redirect()->route('admin.teams.index');
    }

    public function render()
    {
        return view('livewire.admin.team.show')->layout('layouts.admin');
    }
}
