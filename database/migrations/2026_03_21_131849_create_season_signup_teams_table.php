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
        Schema::create('season_team_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('existing_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('existing_venue_id')->nullable()->constrained('venues')->nullOnDelete();
            $table->string('team_name');
            $table->string('venue_name');
            $table->text('venue_address')->nullable();
            $table->string('venue_telephone')->nullable();
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('season_team_entries');
    }
};
