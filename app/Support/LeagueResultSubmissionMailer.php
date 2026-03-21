<?php

namespace App\Support;

use App\Mail\LeagueResultSubmittedMail;
use App\Models\Result;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class LeagueResultSubmissionMailer
{
    public function sendIfNeeded(Result $result): void
    {
        if (! $this->shouldSend($result)) {
            return;
        }

        $result->loadMissing([
            'fixture.section.ruleset',
            'fixture.homeTeam.players',
            'fixture.homeTeam.captain',
            'fixture.awayTeam.players',
            'fixture.awayTeam.captain',
            'submittedBy.team',
        ]);

        $submittedBy = $result->submittedBy;

        if (! $submittedBy instanceof User || blank($submittedBy->email)) {
            return;
        }

        $ccRecipients = $this->teamAdminsForFixture($result)
            ->reject(fn (User $user) => $user->is($submittedBy) || blank($user->email))
            ->pluck('email')
            ->unique()
            ->values()
            ->all();

        Mail::to($submittedBy->email)
            ->cc($ccRecipients)
            ->queue(new LeagueResultSubmittedMail($result));
    }

    private function shouldSend(Result $result): bool
    {
        return $result->is_confirmed
            && filled($result->submitted_by)
            && ($result->wasChanged('submitted_at') || $result->wasChanged('submitted_by'));
    }

    /**
     * @return Collection<int, User>
     */
    private function teamAdminsForFixture(Result $result): Collection
    {
        return collect([
            $result->fixture?->homeTeam,
            $result->fixture?->awayTeam,
        ])
            ->filter(fn ($team) => $team instanceof Team)
            ->flatMap(fn (Team $team) => $this->adminsForTeam($team))
            ->unique(fn (User $user) => $user->getKey())
            ->values();
    }

    /**
     * @return Collection<int, User>
     */
    private function adminsForTeam(Team $team): Collection
    {
        $players = $team->relationLoaded('players') ? $team->players : $team->players()->get();
        $captain = $team->relationLoaded('captain') ? $team->captain : $team->captain()->first();

        return $players
            ->filter(fn (User $user) => $user->isTeamAdmin())
            ->when($captain instanceof User, fn (Collection $users) => $users->push($captain));
    }
}
