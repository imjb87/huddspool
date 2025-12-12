<?php

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
        Schema::create('knockout_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knockout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('knockout_round_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position')->default(1);
            $table->foreignId('home_participant_id')->nullable()->constrained('knockout_participants')->nullOnDelete();
            $table->foreignId('away_participant_id')->nullable()->constrained('knockout_participants')->nullOnDelete();
            $table->foreignId('winner_participant_id')->nullable()->constrained('knockout_participants')->nullOnDelete();
            $table->foreignId('venue_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('starts_at')->nullable();
            $table->unsignedTinyInteger('home_score')->nullable();
            $table->unsignedTinyInteger('away_score')->nullable();
            $table->unsignedTinyInteger('best_of')->nullable();
            $table->foreignId('next_match_id')->nullable()->constrained('knockout_matches')->nullOnDelete();
            $table->enum('next_slot', ['home', 'away'])->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('reported_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knockout_matches');
    }
};
