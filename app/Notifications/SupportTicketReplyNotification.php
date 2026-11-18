<?php

namespace App\Notifications;

use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public SupportTicket $ticket,
        public bool $fromAdministrator,
    ) {
        $this->afterCommit();
        $this->onQueue('emails');
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = $this->fromAdministrator
            ? route('support.tickets.show', $this->ticket)
            : SupportTicketResource::getUrl('view', ['record' => $this->ticket]);

        return (new MailMessage)
            ->subject('New reply on Huddspool support ticket: '.$this->ticket->ticket_number)
            ->greeting('There is a new reply on a support ticket')
            ->line('Ticket '.$this->ticket->ticket_number.' has a new reply.')
            ->action('View support ticket', $url);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Support ticket updated',
            'body' => 'There is a new reply on '.$this->ticket->ticket_number.'.',
            'action_url' => $this->fromAdministrator
                ? route('support.tickets.show', $this->ticket)
                : SupportTicketResource::getUrl('view', ['record' => $this->ticket]),
            'support_ticket_id' => $this->ticket->getKey(),
            'dedupe_key' => 'support-ticket-reply:'.$this->ticket->getKey().':'.$this->ticket->last_activity_at?->timestamp,
        ];
    }
}
