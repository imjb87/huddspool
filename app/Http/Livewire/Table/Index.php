<?php

namespace App\Http\Livewire\Table;

use Livewire\Component;
use App\Models\Ruleset;
use App\Models\Section;

class Index extends Component
{
    public Ruleset $ruleset;
    public $sections;

    public function mount(Ruleset $ruleset)
    {
        $this->ruleset = $ruleset;
        $this->sections = Section::where('ruleset_id', $this->ruleset->id)
            ->whereHas('season', function ($query) {
                $query->where('is_open', true);
            })
            ->get();
    }

    public function render()
    {
        return view('livewire.table.index')
            ->layout('layouts.app', ['title' => $this->ruleset->name . ' Tables']);
    }
}
