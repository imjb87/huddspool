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
        Schema::create('season_knockout_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('knockout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('season_team_entry_id')->nullable()->constrained('season_team_entries')->nullOnDelete();
            $table->foreignId('existing_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->string('entrant_name');
            $table->string('player_one_name')->nullable();
            $table->string('player_two_name')->nullable();
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('season_knockout_entries');
    }
};
