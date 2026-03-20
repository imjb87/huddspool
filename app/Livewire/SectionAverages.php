<?php

namespace App\Livewire;

use App\Models\Section;
use App\Queries\GetSectionAverages;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class SectionAverages extends Component
{
    public Section $section;

    #[Url(except: 1, history: true)]
    public int $page = 1;

    public int $perPage = 10;

    public function mount(Section $section): void
    {
        $this->section = $section;
    }

    public function updatedPage(): void
    {
        $this->page = min(max(1, $this->page), $this->lastPage());
        unset($this->players);
        unset($this->totalPlayers);
    }

    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
            unset($this->players);
            unset($this->totalPlayers);
        }
    }

    public function nextPage(): void
    {
        if ($this->hasNextPage()) {
            $this->page++;
            unset($this->players);
            unset($this->totalPlayers);
        }
    }

    #[Computed]
    public function players(): Collection
    {
        return (new GetSectionAverages($this->section, $this->page, $this->perPage))();
    }

    #[Computed]
    public function totalPlayers(): int
    {
        return (new GetSectionAverages($this->section, 1, $this->perPage))->total();
    }

    public function render(): View
    {
        return view('livewire.section-averages', [
            'players' => $this->players,
            'perPage' => $this->perPage,
            'totalPlayers' => $this->totalPlayers,
        ]);
    }

    private function hasNextPage(): bool
    {
        return $this->page < $this->lastPage();
    }

    private function lastPage(): int
    {
        return max(1, (int) ceil($this->totalPlayers / $this->perPage));
    }
}
