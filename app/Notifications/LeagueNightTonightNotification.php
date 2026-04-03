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

        return [
            'title' => '🎱 League night is tonight',
            'body' => 'Get ready for match night. View your fixture, check your opponents, and make sure you\'re set for tonight.',
            'action_url' => route('fixture.show', $this->fixture),
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
