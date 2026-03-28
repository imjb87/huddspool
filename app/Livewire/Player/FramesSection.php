<?php

namespace App\Livewire\Player;

use App\Models\Section;
use App\Models\User;
use App\Queries\GetPlayerFrames;
use App\Support\FrameSummaryRow;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class FramesSection extends Component
{
    use WithoutUrlPagination;
    use WithPagination;

    public User $player;

    public ?Section $section = null;

    public bool $forAccount = false;

    public function mount(User $player, ?Section $section = null): void
    {
        $this->player = $player;
        $this->section = $section;
    }

    #[Computed]
    public function frames(): LengthAwarePaginator
    {
        return (new GetPlayerFrames($this->player, $this->section, $this->getPage()))();
    }

    #[Computed]
    public function frameRows(): Collection
    {
        return collect($this->frames->items())->map(fn ($frame) => FrameSummaryRow::fromFrame($frame, $this->player->id));
    }

    public function render(): View
    {
        return view('livewire.player.frames-section');
    }
}
