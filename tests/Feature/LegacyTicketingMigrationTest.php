<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacyTicketingMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_retiring_legacy_ticketing_tables_removes_dependents_before_parents(): void
    {
        try {
            Schema::create('ct_departments', function (Blueprint $table): void {
                $table->id();
            });

            Schema::create('ct_forms', function (Blueprint $table): void {
                $table->id();
            });

            Schema::create('ct_ticket_statuses', function (Blueprint $table): void {
                $table->id();
            });

            Schema::create('ct_automation_rules', function (Blueprint $table): void {
                $table->id();
            });

            Schema::create('ct_spam_filters', function (Blueprint $table): void {
                $table->id();
            });

            Schema::create('ct_department_forms', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('department_id')->constrained('ct_departments');
                $table->foreignId('form_id')->constrained('ct_forms');
            });

            Schema::create('ct_form_fields', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('form_id')->constrained('ct_forms');
            });

            Schema::create('ct_department_users', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('department_id')->constrained('ct_departments');
            });

            Schema::create('ct_tickets', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('department_id')->constrained('ct_departments');
                $table->foreignId('form_id')->constrained('ct_forms');
                $table->foreignId('ticket_status_id')->constrained('ct_ticket_statuses');
            });

            Schema::create('ct_ticket_replies', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('ticket_id')->constrained('ct_tickets');
            });

            Schema::create('ct_ticket_activities', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('ticket_id')->constrained('ct_tickets');
            });

            Schema::create('ct_automation_logs', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('automation_rule_id')->constrained('ct_automation_rules');
                $table->foreignId('ticket_id')->constrained('ct_tickets');
            });

            Schema::create('ct_spam_logs', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('spam_filter_id')->nullable()->constrained('ct_spam_filters');
            });

            $migration = require database_path('migrations/2026_11_18_140101_retire_creators_ticketing_tables.php');
            $migration->up();

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
                $this->assertFalse(Schema::hasTable($table));
            }
        } finally {
            Schema::disableForeignKeyConstraints();

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

            Schema::enableForeignKeyConstraints();
        }
    }
}
