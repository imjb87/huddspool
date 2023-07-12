<?php

namespace App\Http\Livewire\Player;

use Livewire\Component;
use App\Models\Ruleset;
use App\Models\Season;

class Index extends Component
{
    public Ruleset $ruleset;
    public $sections;

    public function mount(Ruleset $ruleset)
    {
        $season = Season::where('is_open', true)->first();

        $this->ruleset = $ruleset;

        $this->sections = $ruleset->sections()
            ->where('season_id', $season->id)
            ->get();
    }

    public function render()
    {
        return view('livewire.player.index');
    }
}
