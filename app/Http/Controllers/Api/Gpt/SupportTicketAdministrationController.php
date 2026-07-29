<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Models\GptActionAudit;
use App\Models\User;
use daacreators\CreatorsTicketing\Enums\TicketPriority;
use daacreators\CreatorsTicketing\Models\Ticket;
use daacreators\CreatorsTicketing\Models\TicketStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SupportTicketAdministrationController extends Controller
{
    public function __invoke(Request $request, Ticket $ticket): JsonResponse
    {
        $data = $request->validate([
            'expected_updated_at' => ['required', 'date'],
            'assignee_id' => ['sometimes', 'nullable', 'integer', Rule::exists(User::class, 'id')],
            'ticket_status_id' => ['sometimes', 'integer', Rule::exists(TicketStatus::class, 'id')],
            'priority' => ['sometimes', Rule::enum(TicketPriority::class)],
        ]);
        if (! $ticket->updated_at?->equalTo(Carbon::parse($data['expected_updated_at']))) {
            throw ValidationException::withMessages(['expected_updated_at' => 'The ticket changed after it was inspected. Inspect it again before retrying.']);
        }
        unset($data['expected_updated_at']);
        $fields = ['id', 'assignee_id', 'ticket_status_id', 'priority', 'updated_at'];
        $before = $ticket->only($fields);
        $ticket->update($data);
        $after = $ticket->fresh()->only($fields);
        $audit = GptActionAudit::query()->create(['administrator_id' => $request->user()->id, 'action' => 'update_support_ticket', 'subject_type' => Ticket::class, 'subject_id' => $ticket->id, 'before' => $before, 'after' => $after, 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);

        return response()->json(['message' => 'The support ticket was updated.', 'ticket' => $after, 'audit_id' => $audit->id]);
    }
}
