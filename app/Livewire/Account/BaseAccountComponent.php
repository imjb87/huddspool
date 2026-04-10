<?php

namespace App\Livewire\Account;

use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Queries\GetTeamPlayers;
use App\Support\ResultSubmissionPromptResolver;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

abstract class BaseAccountComponent extends Component
{
    #[Computed]
    public function submissionActions(): ?array
    {
        return $this->resultSubmissionPromptResolver()->promptFor($this->user);
    }

    #[Computed]
    public function user(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->loadMissing([
            'team.venue',
            'team.captain',
        ]);
    }

    #[Computed]
    public function team(): ?Team
    {
        return $this->user->team;
    }

    #[Computed]
    public function currentSection(): ?Section
    {
        return $this->team?->openSection();
    }

    #[Computed]
    public function teamMembers(): Collection
    {
        if (! $this->team) {
            return collect();
        }

        return new GetTeamPlayers($this->team, $this->currentSection)();
    }

    #[Computed]
    public function hasPushSubscriptions(): bool
    {
        return $this->user->pushSubscriptions()->exists();
    }

    #[Computed]
    public function pushDevices(): Collection
    {
        return $this->user->pushSubscriptions()
            ->latest('id')
            ->get()
            ->map(fn ($subscription) => (object) [
                'id' => $subscription->id,
                'label' => $subscription->device_label ?: 'Unknown device',
                'browser' => $subscription->browser,
                'platform' => $subscription->platform,
                'last_used_at' => $subscription->last_used_at,
            ]);
    }

    #[Computed]
    public function webPushConfigured(): bool
    {
        return filled(config('services.web_push.public_key'))
            && filled(config('services.web_push.private_key'))
            && filled(config('services.web_push.subject'));
    }

    protected function resultSubmissionPromptResolver(): ResultSubmissionPromptResolver
    {
        return new ResultSubmissionPromptResolver;
    }
}
