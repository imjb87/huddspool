<?php

namespace App\Notifications;

use App\Models\KnockoutMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KnockoutMatchReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public KnockoutMatch $match,
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
        $this->match->loadMissing([
            'round.knockout',
            'homeParticipant',
            'awayParticipant',
        ]);

        return [
            'title' => 'Knockout match ready',
            'body' => sprintf(
                '%s / %s is now live: %s.',
                $this->match->round?->knockout?->name ?? 'Knockout',
                $this->match->round?->name ?? 'Round',
                $this->match->title()
            ),
            'action_url' => route('knockout.show', $this->match->round->knockout),
            'knockout_match_id' => $this->match->id,
            'knockout_round_id' => $this->match->knockout_round_id,
            'dedupe_key' => sprintf('knockout-ready:%d', $this->match->id),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->match->loadMissing([
            'round.knockout',
        ]);

        return (new MailMessage)
            ->subject(sprintf(
                'Knockout match ready: %s / %s',
                $this->match->round?->knockout?->name ?? 'Knockout',
                $this->match->round?->name ?? 'Round',
            ))
            ->greeting('Your knockout match is ready')
            ->line(sprintf(
                '%s / %s is now live: %s.',
                $this->match->round?->knockout?->name ?? 'Knockout',
                $this->match->round?->name ?? 'Round',
                $this->match->title()
            ))
            ->action('View knockout', route('knockout.show', $this->match->round->knockout));
    }
}
