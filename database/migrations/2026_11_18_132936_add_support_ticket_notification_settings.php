<?php

use App\Notifications\SupportTicketAcknowledgedNotification;
use App\Notifications\SupportTicketReplyNotification;
use App\Notifications\SupportTicketSubmittedNotification;
use App\Notifications\SupportTicketUpdatedNotification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        DB::table('notification_settings')->upsert([
            [
                'notification_type' => SupportTicketAcknowledgedNotification::class,
                'name' => 'Support request acknowledgement',
                'description' => 'When a user submits a support request.',
                'enabled' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'notification_type' => SupportTicketSubmittedNotification::class,
                'name' => 'New support ticket',
                'description' => 'When a selected administrator receives a new support ticket.',
                'enabled' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'notification_type' => SupportTicketReplyNotification::class,
                'name' => 'Support ticket reply',
                'description' => 'When a support ticket receives a new public reply.',
                'enabled' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'notification_type' => SupportTicketUpdatedNotification::class,
                'name' => 'Support ticket status update',
                'description' => 'When an administrator changes a user ticket workflow status.',
                'enabled' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['notification_type'], ['name', 'description', 'updated_at']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('notification_settings')
            ->whereIn('notification_type', [
                SupportTicketAcknowledgedNotification::class,
                SupportTicketSubmittedNotification::class,
                SupportTicketReplyNotification::class,
                SupportTicketUpdatedNotification::class,
            ])
            ->delete();
    }
};
