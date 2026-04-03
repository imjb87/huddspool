<?php

namespace App\Notifications;

use App\Models\Fixture;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LeagueNightTonightNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Fixture $fixture,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->fixture->loadMissing([
            'homeTeam',
            'awayTeam',
        ]);

        $isTeamAdmin = method_exists($notifiable, 'isTeamAdmin') && $notifiable->isTeamAdmin();

        $title = '🎱 League night is tonight';
        $body = $isTeamAdmin
            ? 'It\'s match night. Prepare your team ahead of time, pick your players, and get ready to submit the result.'
            : 'Get ready for match night. View your fixture, check your opponents, and make sure you\'re set for tonight.';
        $actionUrl = $isTeamAdmin
            ? route('result.create', $this->fixture)
            : route('fixture.show', $this->fixture);

        return [
            'title' => $title,
            'body' => $body,
            'action_url' => $actionUrl,
            'fixture_id' => $this->fixture->id,
            'fixture_date' => $this->fixture->fixture_date?->toDateString(),
            'dedupe_key' => sprintf(
                'league-night-tonight:%d:%s',
                $this->fixture->id,
                $this->fixture->fixture_date?->toDateString() ?? 'unknown'
            ),
        ];
    }
}
