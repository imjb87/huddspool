<?php

namespace App\Http\Livewire\Admin\Team;

use Livewire\Component;
use App\Models\Team;
use App\Models\Venue;

class Create extends Component
{
    public $venues;
    public $team;

    protected $rules = [
        'team.name' => 'required|string',
        'team.venue_id' => 'required|string',
    ];

    protected $messages = [
        'team.name.required' => 'The team name is required',
        'team.venue_id.required' => 'The venue is required',
    ];

    public function mount()
    {
        $this->venues = Venue::all();
    }

    public function save()
    {
        $this->validate();

        $team = Team::create([
            'name' => $this->team['name'],
            'venue_id' => $this->team['venue_id']
        ]);

        return redirect()->route('admin.teams.show', $team);
    }

    public function render()
    {
        return view('livewire.admin.team.create')->layout('layouts.admin');
    }
}
