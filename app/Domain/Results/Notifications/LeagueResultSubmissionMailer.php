<?php

namespace App\Domain\Results\Notifications;

use App\Domain\Notifications\DatabaseNotificationDispatcher;
use App\Domain\Notifications\NotificationAudienceResolver;
use App\Mail\LeagueResultSubmittedMail;
use App\Models\Result;
use App\Models\Season;
use App\Notifications\LeagueResultSubmittedNotification;
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

        $mailRecipients = $audienceResolver->teamAdminsForFixture($result->fixture)
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
        if (! $result->is_confirmed
            || blank($result->submitted_by)
            || (! $result->wasChanged('submitted_at') && ! $result->wasChanged('submitted_by'))) {
            return false;
        }

        $seasonId = $result->fixture?->season_id;

        return $seasonId !== null
            && Season::query()->whereKey($seasonId)->where('is_open', true)->exists();
    }
}
