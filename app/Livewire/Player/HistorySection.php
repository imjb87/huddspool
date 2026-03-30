<?php

namespace App\Livewire\Player;

use App\Models\User;
use App\Queries\GetPlayerSeasonHistory;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class HistorySection extends Component
{
    public User $player;

    public int $page = 1;

    public int $perPage = 5;

    public function mount(User $player): void
    {
        $this->player = $player;
    }

    #[Computed]
    public function allHistory(): Collection
    {
        return (new GetPlayerSeasonHistory($this->player))()
            ->reject(fn (array $entry): bool => ($entry['team_expelled'] ?? false) || ($entry['player_expelled'] ?? false))
            ->values();
    }

    #[Computed]
    public function historyRows(): Collection
    {
        return $this->allHistory
            ->forPage($this->page, $this->perPage)
            ->values();
    }

    public function previousPage(): void
    {
        $this->page = max(1, $this->page - 1);

        unset($this->historyRows);
    }

    public function nextPage(): void
    {
        if (! $this->hasNextPage()) {
            return;
        }

        $this->page++;

        unset($this->historyRows);
    }

    public function hasNextPage(): bool
    {
        return $this->page < $this->lastPage();
    }

    public function lastPage(): int
    {
        return max(1, (int) ceil($this->allHistory->count() / $this->perPage));
    }

    public function render(): View
    {
        return view('livewire.player.history-section');
    }
}
