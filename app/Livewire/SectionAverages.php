<?php

namespace App\Livewire;

use App\Models\Section;
use App\Queries\GetSectionAverages;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SectionAverages extends Component
{
    public Section $section;

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

    #[Computed]
    public function players(): Collection
    {
        return (new GetSectionAverages($this->section, $this->page, $this->perPage))();
    }

    public function render(): View
    {
        return view('livewire.section-averages', [
            'players' => $this->players,
            'perPage' => $this->perPage,
        ]);
    }
}
