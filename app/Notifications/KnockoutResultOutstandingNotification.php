<?php

namespace App\Notifications;

use App\Models\KnockoutMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KnockoutResultOutstandingNotification extends Notification implements ShouldQueue
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
            'title' => '🏆 Knockout result is outstanding',
            'body' => sprintf(
                '%s / %s still needs a submitted result: %s.',
                $this->match->round?->knockout?->name ?? 'Knockout',
                $this->match->round?->name ?? 'Round',
                $this->match->title(),
            ),
            'action_url' => route('knockout.matches.submit', $this->match),
            'knockout_match_id' => $this->match->id,
            'knockout_round_id' => $this->match->knockout_round_id,
            'scheduled_for' => ($this->match->starts_at ?? $this->match->round?->scheduled_for)?->toDateString(),
            'dedupe_key' => sprintf(
                'knockout-result-outstanding:%d:%s',
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
                'Knockout result outstanding: %s / %s',
                $this->match->round?->knockout?->name ?? 'Knockout',
                $this->match->round?->name ?? 'Round',
            ))
            ->greeting('Your knockout result is still outstanding')
            ->line(sprintf(
                '%s / %s still needs a submitted result: %s.',
                $this->match->round?->knockout?->name ?? 'Knockout',
                $this->match->round?->name ?? 'Round',
                $this->match->title(),
            ))
            ->action('Submit knockout result', route('knockout.matches.submit', $this->match));
    }
}
