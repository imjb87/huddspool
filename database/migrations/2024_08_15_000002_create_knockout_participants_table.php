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
        Schema::create('knockout_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knockout_id')->constrained()->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->unsignedInteger('seed')->nullable();
            $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('player_one_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('player_two_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knockout_participants');
    }
};
