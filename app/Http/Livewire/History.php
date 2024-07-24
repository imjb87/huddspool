<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Season;
use App\Models\Ruleset;
use App\Models\Section;

class History extends Component
{
    public Season $season;
    public Ruleset $ruleset;
    public $sections;

    public function mount(Season $season, Ruleset $ruleset)
    {
        $this->season = $season;
        $this->ruleset = $ruleset;
        $this->sections = Section::where('ruleset_id', $this->ruleset->id)
            ->where('season_id', $this->season->id)
            ->get();
    }

    public function render()
    {
        return view('livewire.history');
    }
}
