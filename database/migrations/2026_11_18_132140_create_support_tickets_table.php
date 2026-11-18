<?php

use App\Enums\SupportTicketPriority;
use App\Enums\SupportTicketStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('requester_name');
            $table->string('requester_email');
            $table->text('message');
            $table->string('status', 32)->default(SupportTicketStatus::Open->value)->index();
            $table->string('priority', 32)->default(SupportTicketPriority::Low->value)->index();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_seen')->default(false);
            $table->foreignId('seen_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('seen_at')->nullable();
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
