<?php

namespace App\Notifications;

use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public SupportTicket $ticket,
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
        return (new MailMessage)
            ->subject('New Huddspool support ticket: '.$this->ticket->ticket_number)
            ->greeting('A new support ticket has been submitted')
            ->line($this->ticket->requester_name.' has submitted a support request.')
            ->line($this->ticket->message)
            ->action('View support ticket', SupportTicketResource::getUrl('view', ['record' => $this->ticket]));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New support ticket',
            'body' => $this->ticket->requester_name.' submitted '.$this->ticket->ticket_number.'.',
            'action_url' => SupportTicketResource::getUrl('view', ['record' => $this->ticket]),
            'support_ticket_id' => $this->ticket->getKey(),
            'dedupe_key' => 'support-ticket-submitted:'.$this->ticket->getKey(),
        ];
    }
}
