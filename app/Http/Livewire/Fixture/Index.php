<?php

namespace App\Http\Livewire\Fixture;

use Livewire\Component;
use App\Models\Ruleset;
use App\Models\Season;
use Illuminate\Support\Arr;

class Index extends Component
{
    public Ruleset $ruleset;
    public $page = 1;
    public $sections;

    public function mount(Ruleset $ruleset)
    {
        $season = Season::where('is_open', true)->first();

        if( !request()->page ) {
            Arr::map($season->dates, function ($date, $key) use (&$page) {
                if( date('W', strtotime($date)) == date('W') ) {
                    $this->page = $key + 1;
                }
            });
        } else {
            $this->page = request()->page;
        }

        $this->ruleset = $ruleset;

        $this->sections = $ruleset->sections()
            ->where('season_id', $season->id)
            ->get();
    }

    public function render()
    {
        return view('livewire.fixture.index')->layout('layouts.app');
    }
}
