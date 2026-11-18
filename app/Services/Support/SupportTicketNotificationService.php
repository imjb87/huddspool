<?php

namespace App\Services\Support;

use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportTicketAcknowledgedNotification;
use App\Notifications\SupportTicketReplyNotification;
use App\Notifications\SupportTicketSubmittedNotification;
use App\Notifications\SupportTicketUpdatedNotification;
use App\Support\Notifications\DatabaseNotificationDispatcher;
use Illuminate\Database\Eloquent\Builder;

class SupportTicketNotificationService
{
    public function __construct(
        private DatabaseNotificationDispatcher $dispatcher,
    ) {}

    public function ticketSubmitted(SupportTicket $ticket, User $requester): void
    {
        $requester->notify(new SupportTicketAcknowledgedNotification($ticket));

        $this->dispatcher->sendOnce(
            $this->recipients()->where('id', '!=', $requester->getKey())->get(),
            new SupportTicketSubmittedNotification($ticket),
        );
    }

    public function replyAdded(SupportTicket $ticket, User $author, bool $isInternalNote): void
    {
        if ($isInternalNote) {
            return;
        }

        if ($ticket->requester?->is($author)) {
            $this->dispatcher->sendOnce(
                $this->recipients()->where('id', '!=', $author->getKey())->get(),
                new SupportTicketReplyNotification($ticket, fromAdministrator: false),
            );

            return;
        }

        if ($ticket->requester) {
            $ticket->requester->notify(new SupportTicketReplyNotification($ticket, fromAdministrator: true));
        }
    }

    public function ticketUpdated(SupportTicket $ticket, User $actor): void
    {
        if ($ticket->requester && ! $ticket->requester->is($actor)) {
            $ticket->requester->notify(new SupportTicketUpdatedNotification($ticket));
        }
    }

    private function recipients(): Builder
    {
        return User::query()
            ->where('receives_support_ticket_notifications', true)
            ->whereNull('deleted_at')
            ->whereNotNull('email');
    }
}
