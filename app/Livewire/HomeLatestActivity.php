<?php

namespace App\Livewire;

use App\Data\HomeActivityItem;
use App\Queries\GetRecentActivity;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class HomeLatestActivity extends Component
{
    public int $pollInterval = 90;

    public int $visibleRows = 8;

    public function mount(): void
    {
        $user = auth()->user();

        if ($user && ($user->isAdmin() || $user->isTeamAdmin())) {
            $this->pollInterval = 30;
        }
    }

    #[Computed]
    public function updates(): Collection
    {
        $user = auth()->user();

        $recentActivity = app(GetRecentActivity::class)($this->visibleRows);

        return $recentActivity->map(function (HomeActivityItem $item) use ($user): array {
            $canResume = false;

            if ($user && ($user->isAdmin() || $user->isTeamAdmin())) {
                $teamId = $user->team?->id;
                $canResume = $user->isAdmin()
                    || ($teamId !== null && in_array($teamId, [$item->home_team_id, $item->away_team_id], true));
            }

            return [
                'item' => $item,
                'canResume' => $canResume && ! $item->is_confirmed,
            ];
        });
    }

    public function render(): View
    {
        return view('livewire.home-latest-activity', [
            'updates' => $this->updates,
            'pollInterval' => $this->pollInterval,
        ]);
    }
}
