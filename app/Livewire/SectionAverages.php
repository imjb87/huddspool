<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Section;
use App\Queries\GetSectionAverages;

class SectionAverages extends Component
{
    public Section $section;
    public int $page = 1;
    public int $perPage = 10;

    public function mount(Section $section)
    {
        $this->section = $section;
    }

    public function updatedPage()
    {
        $this->page = max(1, $this->page);
    }

    public function previousPage()
    {
        if ($this->page > 1) {
            $this->page--;
        }
    }

    public function nextPage()
    {
        if ($this->page < 10) {
            $this->page++;
        }
    }

    public function render()
    {
        $players = (new GetSectionAverages($this->section, $this->page, $this->perPage)());

        return view('livewire.section-averages', [
            'players' => $players,
            'perPage' => $this->perPage,
        ]);
    }    
}
