<?php

namespace App\Livewire\Admin\Expulsion;

use Livewire\Component;
use App\Models\Team;
use App\Models\Season;
use App\Models\Expulsion;
use App\Models\User;

class Create extends Component
{
    public Season $season;
    public $expellable_type = 'App\Models\Team';
    public $teams;
    public $users;
    public $team_id;
    public $user_id;

    public function mount(Season $season)
    {
        $this->season = $season;

        $this->teams = Team::all()->sortBy('name');
        $this->users = User::all()->sortBy('name');
    }

    public function save()
    {
        Expulsion::create([
            'season_id' => $this->season->id,
            'expellable_id' => $this->expellable_type === 'App\Models\Team' ? $this->team_id : $this->user_id,
            'expellable_type' => $this->expellable_type,
            'reason' => 'Expelled',
            'date' => now(),
        ]);

        return redirect()->route('admin.seasons.show', $this->season);
    }

    public function render()
    {
        return view('livewire.admin.expulsion.create')->layout('layouts.admin');
    }
}
