<?php

namespace App\Support;

use App\Mail\LeagueResultSubmittedMail;
use App\Models\Result;
use App\Models\Team;
use App\Notifications\LeagueResultSubmittedNotification;
use App\Support\Notifications\DatabaseNotificationDispatcher;
use App\Support\Notifications\NotificationAudienceResolver;
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

        $audienceResolver = new NotificationAudienceResolver;
        $dispatcher = new DatabaseNotificationDispatcher;

        $mailRecipients = $this->teamAdminsForFixture($result, $audienceResolver)
            ->pluck('email')
            ->filter(fn (?string $email): bool => filled($email))
            ->unique()
            ->values()
            ->all();

        $dispatcher->sendOnce(
            $audienceResolver->resultSubmissionRecipients($result),
            new LeagueResultSubmittedNotification($result),
        );

        if ($mailRecipients === []) {
            return;
        }

        Mail::to(array_shift($mailRecipients))
            ->cc($mailRecipients)
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
    private function teamAdminsForFixture(Result $result, NotificationAudienceResolver $audienceResolver): Collection
    {
        return collect([
            $result->fixture?->homeTeam,
            $result->fixture?->awayTeam,
        ])
            ->filter(fn ($team) => $team instanceof Team)
            ->flatMap(fn (Team $team) => $audienceResolver->adminsForTeam($team))
            ->unique(fn (User $user) => $user->getKey())
            ->values();
    }
}
