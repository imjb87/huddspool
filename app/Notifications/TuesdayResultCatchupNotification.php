<?php

namespace App\Notifications;

use App\Models\Fixture;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class TuesdayResultCatchupNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Fixture $fixture,
        public Carbon $fixtureDate,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->fixture->loadMissing([
            'homeTeam',
            'awayTeam',
        ]);

        return (new MailMessage)
            ->subject(sprintf(
                'Tuesday result still outstanding: %s vs %s',
                $this->fixture->homeTeam?->name ?? 'TBC',
                $this->fixture->awayTeam?->name ?? 'TBC',
            ))
            ->greeting('Your Tuesday result is still outstanding')
            ->line(sprintf(
                '%s vs %s from %s still needs a submitted result.',
                $this->fixture->homeTeam?->name ?? 'TBC',
                $this->fixture->awayTeam?->name ?? 'TBC',
                $this->fixtureDate->format('j F'),
            ))
            ->action('Submit result', route('result.create', $this->fixture));
    }

    public function toArray(object $notifiable): array
    {
        $this->fixture->loadMissing([
            'homeTeam',
            'awayTeam',
        ]);

        return [
            'title' => '⏰ Tuesday result is still outstanding',
            'body' => sprintf(
                '%s vs %s from %s still needs a submitted result.',
                $this->fixture->homeTeam?->name ?? 'TBC',
                $this->fixture->awayTeam?->name ?? 'TBC',
                $this->fixtureDate->format('j F'),
            ),
            'action_url' => route('result.create', $this->fixture),
            'fixture_id' => $this->fixture->id,
            'fixture_date' => $this->fixtureDate->toDateString(),
            'dedupe_key' => sprintf(
                'tuesday-result-catchup:%d:%s',
                $this->fixture->id,
                $this->fixtureDate->toDateString(),
            ),
        ];
    }
}
