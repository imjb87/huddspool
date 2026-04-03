<?php

namespace App\Notifications;

use App\Models\KnockoutMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KnockoutMatchReminderNotification extends Notification implements ShouldQueue
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
            'title' => 'Knockout match is today',
            'body' => sprintf(
                '%s / %s is scheduled for today: %s.',
                $this->match->round?->knockout?->name ?? 'Knockout',
                $this->match->round?->name ?? 'Round',
                $this->match->title(),
            ),
            'action_url' => route('knockout.show', $this->match->round->knockout),
            'knockout_match_id' => $this->match->id,
            'knockout_round_id' => $this->match->knockout_round_id,
            'scheduled_for' => ($this->match->starts_at ?? $this->match->round?->scheduled_for)?->toDateString(),
            'dedupe_key' => sprintf(
                'knockout-reminder:%d:%s',
                $this->match->id,
                ($this->match->starts_at ?? $this->match->round?->scheduled_for)?->toDateString() ?? 'unknown'
            ),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->match->loadMissing([
            'round.knockout',
        ]);

        return (new MailMessage)
            ->subject(sprintf(
                'Knockout match today: %s / %s',
                $this->match->round?->knockout?->name ?? 'Knockout',
                $this->match->round?->name ?? 'Round',
            ))
            ->greeting('Your knockout match is today')
            ->line(sprintf(
                '%s / %s is scheduled for today: %s.',
                $this->match->round?->knockout?->name ?? 'Knockout',
                $this->match->round?->name ?? 'Round',
                $this->match->title(),
            ))
            ->action('View knockout', route('knockout.show', $this->match->round->knockout));
    }
}
