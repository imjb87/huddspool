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
        Schema::table('knockout_matches', function (Blueprint $table) {
            $table->foreignId('forfeit_participant_id')
                ->nullable()
                ->after('winner_participant_id')
                ->constrained('knockout_participants')
                ->nullOnDelete();
            $table->text('forfeit_reason')->nullable()->after('forfeit_participant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knockout_matches', function (Blueprint $table) {
            $table->dropForeign(['forfeit_participant_id']);
            $table->dropColumn(['forfeit_participant_id', 'forfeit_reason']);
        });
    }
};
