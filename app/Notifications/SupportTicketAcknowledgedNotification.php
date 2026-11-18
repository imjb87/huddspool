<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketAcknowledgedNotification extends Notification implements ShouldQueue
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
            ->subject('We received your Huddspool support request')
            ->greeting('Your support request has been received')
            ->line('Your request '.$this->ticket->ticket_number.' has been sent to the Huddspool admin team.')
            ->line('You can follow replies and status changes from your support area.')
            ->action('View your support ticket', route('support.tickets.show', $this->ticket));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Support request received',
            'body' => 'Your support request '.$this->ticket->ticket_number.' has been received.',
            'action_url' => route('support.tickets.show', $this->ticket),
            'support_ticket_id' => $this->ticket->getKey(),
            'dedupe_key' => 'support-ticket-acknowledged:'.$this->ticket->getKey(),
        ];
    }
}
