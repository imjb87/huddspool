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
    public function resultSubmissionPrompt(): ?object
    {
        $prompt = $this->resultSubmissionPromptResolver()->promptFor($this->user);

        return $prompt ? (object) $prompt : null;
    }

    protected function resultSubmissionPromptResolver(): ResultSubmissionPromptResolver
    {
        return new ResultSubmissionPromptResolver;
    }
}
