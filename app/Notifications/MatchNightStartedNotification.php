<?php

namespace App\Notifications;

use App\Models\Fixture;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MatchNightStartedNotification extends Notification
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
            'title' => 'Your match has started',
            'body' => 'Your match has started. Pick your players, keep things moving, and get ready to submit the result.',
            'action_url' => route('result.create', $this->fixture),
            'fixture_id' => $this->fixture->id,
            'fixture_date' => $this->fixture->fixture_date?->toDateString(),
            'dedupe_key' => sprintf(
                'match-night-started:%d:%s',
                $this->fixture->id,
                $this->fixture->fixture_date?->toDateString() ?? 'unknown'
            ),
        ];
    }
}
