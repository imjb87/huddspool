<?php

namespace App\Livewire\Standings;

use App\Models\Section;
use Livewire\Component;

class Show extends Component
{
    public Section $section;

    public function render()
    {
        return view('livewire.standings.show', [
            'section' => $this->section,
            'standings' => $this->section->standings(),
        ]);
    }
}
