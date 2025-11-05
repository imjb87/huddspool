<?php

namespace App\Livewire\Player;

use App\Models\Section;
use App\Queries\GetSectionAverages;
use Livewire\Component;

class SectionShow extends Component
{
    public Section $section;
    public bool $history = false;
    public int $page = 1;
    public int $perPage = 10;

    public function mount(Section $section): void
    {
        $this->section = $section;
    }

    public function updatedPage(): void
    {
        $this->page = max(1, $this->page);
    }

    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
        }
    }

    public function nextPage(): void
    {
        if ($this->page < 10) {
            $this->page++;
        }
    }

    public function render()
    {
        $players = (new GetSectionAverages($this->section, $this->page, $this->perPage))();

        return view('livewire.player.section-show', [
            'section' => $this->section,
            'players' => $players,
            'history' => $this->history,
            'page' => $this->page,
            'perPage' => $this->perPage,
        ]);
    }
}
