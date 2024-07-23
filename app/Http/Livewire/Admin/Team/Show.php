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

    public function fold()
    {
        // unassign captain
        $this->team->captain_id = null;

        // remove all players from the team
        $this->team->players()->update(['team_id' => null]);

        // remove the team from the venue
        $this->team->venue_id = null;

        // set the folded_at timestamp
        $this->team->folded_at = now();

        // save the team
        $this->team->save();

        return redirect()->route('admin.teams.index');
    }

    public function delete()
    {
        $this->team->players()->update(['team_id' => null]);

        $this->team->delete();

        return redirect()->route('admin.teams.index');
    }

    public function render()
    {
        return view('livewire.admin.team.show')->layout('layouts.admin');
    }
}
