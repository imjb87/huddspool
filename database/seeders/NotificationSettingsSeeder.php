<?php

namespace Database\Seeders;

use App\Models\NotificationSetting;
use App\Notifications\FixtureResultOutstandingNotification;
use App\Notifications\KnockoutMatchReadyNotification;
use App\Notifications\KnockoutMatchReminderNotification;
use App\Notifications\KnockoutResultOutstandingNotification;
use App\Notifications\LeagueNightTonightNotification;
use App\Notifications\LeagueResultSubmittedNotification;
use App\Notifications\MatchNightStartedNotification;
use App\Notifications\NewsPublishedNotification;
use App\Notifications\SupportTicketAcknowledgedNotification;
use App\Notifications\SupportTicketReplyNotification;
use App\Notifications\SupportTicketSubmittedNotification;
use App\Notifications\SupportTicketUpdatedNotification;
use App\Notifications\TuesdayResultCatchupNotification;
use Illuminate\Database\Seeder;

class NotificationSettingsSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->settings() as $notificationType => [$name, $description]) {
            NotificationSetting::query()->firstOrCreate(
                ['notification_type' => $notificationType],
                [
                    'name' => $name,
                    'description' => $description,
                    'enabled' => true,
                ],
            );
        }
    }

    /**
     * @return array<class-string, array{string, string}>
     */
    private function settings(): array
    {
        return [
            LeagueNightTonightNotification::class => [
                'League night tonight',
                'On the day of a league fixture.',
            ],
            MatchNightStartedNotification::class => [
                'Match night started',
                'When a league match is ready to begin.',
            ],
            FixtureResultOutstandingNotification::class => [
                'Outstanding league result',
                'When a completed league fixture still needs a result.',
            ],
            TuesdayResultCatchupNotification::class => [
                'Tuesday result catch-up',
                'A Sunday reminder for an outstanding Tuesday result.',
            ],
            KnockoutMatchReminderNotification::class => [
                'Knockout match reminder',
                'Before an upcoming knockout match.',
            ],
            KnockoutResultOutstandingNotification::class => [
                'Outstanding knockout result',
                'When a completed knockout match still needs a result.',
            ],
            KnockoutMatchReadyNotification::class => [
                'Knockout match ready',
                'When the teams in a knockout match are confirmed.',
            ],
            LeagueResultSubmittedNotification::class => [
                'League result submitted',
                'When a league result is submitted for confirmation.',
            ],
            NewsPublishedNotification::class => [
                'News published',
                'When a news article is published.',
            ],
            SupportTicketAcknowledgedNotification::class => [
                'Support request acknowledgement',
                'When a user submits a support request.',
            ],
            SupportTicketSubmittedNotification::class => [
                'New support ticket',
                'When a selected administrator receives a new support ticket.',
            ],
            SupportTicketReplyNotification::class => [
                'Support ticket reply',
                'When a support ticket receives a new public reply.',
            ],
            SupportTicketUpdatedNotification::class => [
                'Support ticket status update',
                'When an administrator changes a user ticket workflow status.',
            ],
        ];
    }
}
