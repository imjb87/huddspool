<?php

namespace App\Livewire\Standings;

use App\Models\Section;
use App\Support\StandingSummaryRow;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public Section $section;

    public bool $history = false;

    #[Computed]
    public function standings(): Collection
    {
        return $this->section->standings();
    }

    #[Computed]
    public function summaryCopy(): string
    {
        return $this->history
            ? 'Archived positions, results and points for this section.'
            : 'Current positions, results and points for this section.';
    }

    #[Computed]
    public function standingRows(): Collection
    {
        return $this->standings
            ->values()
            ->map(fn ($team, $index) => StandingSummaryRow::fromStanding($team, $index + 1, $this->history));
    }

    public function render(): View
    {
        return view($this->history ? 'livewire.standings.history' : 'livewire.standings.show', [
            'section' => $this->section,
            'standings' => $this->standings,
            'standingRows' => $this->standingRows,
            'summaryCopy' => $this->summaryCopy,
        ]);
    }
}
