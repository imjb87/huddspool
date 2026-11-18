<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketUpdatedNotification extends Notification implements ShouldQueue
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
            ->subject('Your Huddspool support ticket was updated')
            ->greeting('Your support ticket was updated')
            ->line('The status of '.$this->ticket->ticket_number.' is now '.$this->ticket->status?->label().'.')
            ->action('View your support ticket', route('support.tickets.show', $this->ticket));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Support ticket status updated',
            'body' => 'Your support ticket '.$this->ticket->ticket_number.' is now '.$this->ticket->status?->label().'.',
            'action_url' => route('support.tickets.show', $this->ticket),
            'support_ticket_id' => $this->ticket->getKey(),
            'dedupe_key' => 'support-ticket-updated:'.$this->ticket->getKey().':'.$this->ticket->updated_at?->timestamp,
        ];
    }
}
