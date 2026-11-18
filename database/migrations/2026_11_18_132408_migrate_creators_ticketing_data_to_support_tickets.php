<?php

use App\Enums\SupportTicketPriority;
use App\Enums\SupportTicketStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $legacyTicketsTable = 'ct_tickets';

        if (! Schema::hasTable($legacyTicketsTable)) {
            return;
        }

        $legacyStatuses = Schema::hasTable('ct_ticket_statuses')
            ? DB::table('ct_ticket_statuses')->pluck('slug', 'id')->all()
            : [];
        $ticketIds = [];

        foreach (DB::table($legacyTicketsTable)->orderBy('id')->get() as $legacyTicket) {
            $customFields = is_string($legacyTicket->custom_fields ?? null)
                ? json_decode($legacyTicket->custom_fields, true)
                : ($legacyTicket->custom_fields ?? []);
            $customFields = is_array($customFields) ? $customFields : [];
            $userId = $this->existingUserId($legacyTicket->user_id ?? null);
            $requesterName = $legacyTicket->guest_name
                ?? $customFields['name']
                ?? ($userId ? DB::table('users')->where('id', $userId)->value('name') : null)
                ?? 'Huddspool user';
            $requesterEmail = $legacyTicket->guest_email
                ?? $customFields['email']
                ?? ($userId ? DB::table('users')->where('id', $userId)->value('email') : null)
                ?? '';
            $message = (string) ($customFields['message'] ?? $customFields['description'] ?? '');
            $statusSlug = strtolower(str_replace('_', '-', (string) ($legacyStatuses[$legacyTicket->ticket_status_id] ?? 'open')));
            $status = SupportTicketStatus::tryFrom($statusSlug)
                ?? SupportTicketStatus::Open;
            $priority = SupportTicketPriority::tryFrom(strtolower((string) ($legacyTicket->priority ?? 'low')))
                ?? SupportTicketPriority::Low;
            $ticketNumber = filled($legacyTicket->ticket_uid ?? null)
                ? $legacyTicket->ticket_uid
                : 'TKT-LEGACY-'.$legacyTicket->id;
            $existingId = DB::table('support_tickets')->where('ticket_number', $ticketNumber)->value('id');

            if ($existingId) {
                $ticketIds[$legacyTicket->id] = $existingId;

                continue;
            }

            $ticketIds[$legacyTicket->id] = DB::table('support_tickets')->insertGetId([
                'ticket_number' => $ticketNumber,
                'user_id' => $userId,
                'requester_name' => (string) $requesterName,
                'requester_email' => (string) $requesterEmail,
                'message' => $message,
                'status' => $status->value,
                'priority' => $priority->value,
                'assignee_id' => $this->existingUserId($legacyTicket->assignee_id ?? null),
                'is_seen' => (bool) ($legacyTicket->is_seen ?? false),
                'seen_by' => $this->existingUserId($legacyTicket->seen_by ?? null),
                'seen_at' => $legacyTicket->seen_at ?? null,
                'last_activity_at' => $legacyTicket->last_activity_at ?? $legacyTicket->updated_at,
                'created_at' => $legacyTicket->created_at,
                'updated_at' => $legacyTicket->updated_at,
            ]);
        }

        if (Schema::hasTable('ct_ticket_replies')) {
            foreach (DB::table('ct_ticket_replies')->orderBy('id')->get() as $legacyReply) {
                $supportTicketId = $ticketIds[$legacyReply->ticket_id] ?? null;

                if (! $supportTicketId) {
                    continue;
                }

                DB::table('support_ticket_replies')->insert([
                    'support_ticket_id' => $supportTicketId,
                    'user_id' => $this->existingUserId($legacyReply->user_id ?? null),
                    'content' => $legacyReply->content,
                    'is_internal_note' => (bool) ($legacyReply->is_internal_note ?? false),
                    'is_seen' => (bool) ($legacyReply->is_seen ?? false),
                    'seen_by' => $this->existingUserId($legacyReply->seen_by ?? null),
                    'seen_at' => $legacyReply->seen_at ?? null,
                    'created_at' => $legacyReply->created_at,
                    'updated_at' => $legacyReply->updated_at,
                ]);
            }
        }

        if (Schema::hasTable('ct_ticket_activities')) {
            foreach (DB::table('ct_ticket_activities')->orderBy('id')->get() as $legacyActivity) {
                $supportTicketId = $ticketIds[$legacyActivity->ticket_id] ?? null;

                if (! $supportTicketId) {
                    continue;
                }

                DB::table('support_ticket_activities')->insert([
                    'support_ticket_id' => $supportTicketId,
                    'user_id' => $this->existingUserId($legacyActivity->user_id ?? null),
                    'description' => $legacyActivity->description,
                    'old_value' => $legacyActivity->old_value,
                    'new_value' => $legacyActivity->new_value,
                    'created_at' => $legacyActivity->created_at,
                    'updated_at' => $legacyActivity->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Migrated records are retained because this migration does not own the
        // application-owned support ticket tables.
    }

    private function existingUserId(mixed $userId): ?int
    {
        if (! filled($userId)) {
            return null;
        }

        return DB::table('users')->where('id', $userId)->exists() ? (int) $userId : null;
    }
};
