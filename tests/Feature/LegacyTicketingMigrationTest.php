<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacyTicketingMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_retiring_legacy_ticketing_tables_removes_automation_logs_before_tickets(): void
    {
        Schema::create('ct_tickets', function (Blueprint $table): void {
            $table->id();
        });

        Schema::create('ct_automation_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ticket_id')->constrained('ct_tickets');
        });

        $migration = require database_path('migrations/2026_11_18_140101_retire_creators_ticketing_tables.php');

        try {
            $migration->up();

            $this->assertFalse(Schema::hasTable('ct_automation_logs'));
            $this->assertFalse(Schema::hasTable('ct_tickets'));
        } finally {
            Schema::disableForeignKeyConstraints();
            Schema::dropIfExists('ct_automation_logs');
            Schema::dropIfExists('ct_tickets');
            Schema::enableForeignKeyConstraints();
        }
    }
}
