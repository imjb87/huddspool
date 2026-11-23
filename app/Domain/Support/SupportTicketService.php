<?php

namespace App\Domain\Support;

use App\Domain\Support\Actions\AddSupportTicketReply;
use App\Domain\Support\Actions\SubmitSupportTicket;
use App\Domain\Support\Actions\UpdateSupportTicket;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\User;

class SupportTicketService
{
    public function __construct(
        private SubmitSupportTicket $submitTicket,
        private AddSupportTicketReply $addReply,
        private UpdateSupportTicket $updateTicket,
    ) {}

    /**
     * @param  array{name: string, email: string, message: string}  $data
     */
    public function submit(User $requester, array $data): SupportTicket
    {
        return $this->submitTicket->execute($requester, $data);
    }

    public function addReply(SupportTicket $ticket, User $author, string $content, bool $isInternalNote = false): SupportTicketReply
    {
        return $this->addReply->execute($ticket, $author, $content, $isInternalNote);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(SupportTicket $ticket, array $attributes, User $actor): SupportTicket
    {
        return $this->updateTicket->execute($ticket, $attributes, $actor);
    }
}
