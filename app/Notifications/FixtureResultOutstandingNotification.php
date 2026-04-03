<?php

namespace App\Notifications;

use App\Models\Fixture;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FixtureResultOutstandingNotification extends Notification implements ShouldQueue
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
        return ['database', 'mail'];
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
            'title' => 'Your match is outstanding',
            'body' => sprintf(
                '%s vs %s still needs a submitted result.',
                $this->fixture->homeTeam?->name ?? 'TBC',
                $this->fixture->awayTeam?->name ?? 'TBC',
            ),
            'action_url' => route('result.create', $this->fixture),
            'fixture_id' => $this->fixture->id,
            'fixture_date' => $this->fixture->fixture_date?->toDateString(),
            'dedupe_key' => sprintf(
                'fixture-result-outstanding:%d:%s',
                $this->fixture->id,
                $this->fixture->fixture_date?->toDateString() ?? 'unknown'
            ),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->fixture->loadMissing([
            'homeTeam',
            'awayTeam',
        ]);

        return (new MailMessage)
            ->subject(sprintf(
                'Match result outstanding: %s vs %s',
                $this->fixture->homeTeam?->name ?? 'TBC',
                $this->fixture->awayTeam?->name ?? 'TBC',
            ))
            ->greeting('Your match result is still outstanding')
            ->line(sprintf(
                '%s vs %s still needs a submitted result.',
                $this->fixture->homeTeam?->name ?? 'TBC',
                $this->fixture->awayTeam?->name ?? 'TBC',
            ))
            ->action('Submit result', route('result.create', $this->fixture));
    }
}
