<?php

namespace App\Http\Livewire\Admin\Team;

use Livewire\Component;
use App\Models\Team;
use App\Models\Venue;

class Edit extends Component
{
    public Team $team;
    public $venues;

    protected $rules = [
        'team.name' => 'required|string',
        'team.shortname' => 'nullable|string',
        'team.venue_id' => 'required|integer',
        'team.captain_id' => 'nullable|integer',
    ];

    protected $messages = [
        'team.name.required' => 'The team name is required',
        'team.venue_id.required' => 'The venue is required',
        'team.captain_id.integer' => 'A captain must be selected',
    ];

    public function mount(Team $team)
    {
        $this->team = $team;
        $this->venues = Venue::all();
    }

    public function save()
    {
        $this->validate();

        $this->team->save();

        return redirect()->route('admin.teams.show', $this->team);
    }

    public function render()
    {
        return view('livewire.admin.team.edit')->layout('layouts.admin');
    }
}
