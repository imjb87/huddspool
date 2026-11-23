<?php

namespace App\Domain\Support\Actions;

use App\Domain\Support\Notifications\SupportTicketNotificationService;
use App\Enums\SupportTicketStatus;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class SubmitSupportTicket
{
    public function __construct(
        private SupportTicketNotificationService $notifications,
    ) {}

    /**
     * @param  array{name: string, email: string, message: string}  $data
     */
    public function execute(User $requester, array $data): SupportTicket
    {
        $ticket = DB::transaction(function () use ($requester, $data): SupportTicket {
            $ticket = SupportTicket::query()->create([
                'user_id' => $requester->getKey(),
                'requester_name' => $data['name'],
                'requester_email' => $data['email'],
                'message' => $data['message'],
                'status' => SupportTicketStatus::Open,
            ]);

            $ticket->replies()->create([
                'user_id' => $requester->getKey(),
                'content' => $data['message'],
                'is_internal_note' => false,
            ]);

            return $ticket->load('replies');
        });

        $this->notifications->ticketSubmitted($ticket, $requester);

        return $ticket;
    }
}
