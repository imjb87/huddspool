<?php

namespace App\Livewire\Standings;

use App\Models\Section;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public Section $section;

    #[Computed]
    public function standings(): Collection
    {
        return $this->section->standings();
    }

    public function render(): View
    {
        return view('livewire.standings.show', [
            'section' => $this->section,
            'standings' => $this->standings,
        ]);
    }
}
