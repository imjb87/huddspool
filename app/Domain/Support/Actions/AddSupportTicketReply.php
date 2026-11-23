<?php

namespace App\Domain\Support\Actions;

use App\Domain\Support\Notifications\SupportTicketNotificationService;
use App\Enums\SupportTicketStatus;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class AddSupportTicketReply
{
    public function __construct(
        private SupportTicketNotificationService $notifications,
    ) {}

    public function execute(SupportTicket $ticket, User $author, string $content, bool $isInternalNote = false): SupportTicketReply
    {
        $reply = DB::transaction(function () use ($ticket, $author, $content, $isInternalNote): SupportTicketReply {
            $reply = $ticket->replies()->create([
                'user_id' => $author->getKey(),
                'content' => $content,
                'is_internal_note' => $isInternalNote,
            ]);

            $ticket->forceFill([
                'last_activity_at' => now(),
                'is_seen' => false,
                ...(! $isInternalNote ? ['status' => SupportTicketStatus::Answered] : []),
            ])->save();

            $ticket->activities()->create([
                'user_id' => $author->getKey(),
                'description' => $isInternalNote ? 'Internal note added' : 'Reply added',
                'new_value' => $content,
            ]);

            return $reply;
        });

        $this->notifications->replyAdded($ticket->load('requester'), $author, $isInternalNote);

        return $reply;
    }
}
