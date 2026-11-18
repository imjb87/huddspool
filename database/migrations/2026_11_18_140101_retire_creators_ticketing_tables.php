<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ([
            'ct_automation_logs',
            'ct_ticket_activities',
            'ct_ticket_replies',
            'ct_tickets',
            'ct_spam_logs',
            'ct_form_fields',
            'ct_department_forms',
            'ct_department_users',
            'ct_spam_filters',
            'ct_automation_rules',
            'ct_ticket_statuses',
            'ct_forms',
            'ct_departments',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The legacy package tables are intentionally not restored.
    }
};
