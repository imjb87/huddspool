<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixtures', function (Blueprint $table): void {
            $table->index(
                ['section_id', 'week', 'fixture_date'],
                'fixtures_section_week_date_index',
            );
        });

        Schema::table('results', function (Blueprint $table): void {
            $table->index(
                ['fixture_id', 'is_confirmed'],
                'results_fixture_confirmed_index',
            );
            $table->index(
                ['section_id', 'is_confirmed'],
                'results_section_confirmed_index',
            );
        });

        Schema::table('frames', function (Blueprint $table): void {
            $table->index(['result_id', 'id'], 'frames_result_id_id_index');
            $table->index(['home_player_id', 'result_id'], 'frames_home_player_result_index');
            $table->index(['away_player_id', 'result_id'], 'frames_away_player_result_index');
        });

        Schema::table('knockout_matches', function (Blueprint $table): void {
            $table->index(
                ['knockout_id', 'knockout_round_id'],
                'knockout_matches_knockout_round_index',
            );
            $table->index(
                ['next_match_id', 'next_slot'],
                'knockout_matches_next_match_slot_index',
            );
        });

        Schema::dropIfExists('fixture_result_locks');
    }

    public function down(): void
    {
        Schema::table('knockout_matches', function (Blueprint $table): void {
            $table->dropIndex('knockout_matches_knockout_round_index');
            $table->dropIndex('knockout_matches_next_match_slot_index');
        });

        Schema::table('frames', function (Blueprint $table): void {
            $table->dropIndex('frames_result_id_id_index');
            $table->dropIndex('frames_home_player_result_index');
            $table->dropIndex('frames_away_player_result_index');
        });

        Schema::table('results', function (Blueprint $table): void {
            $table->dropIndex('results_fixture_confirmed_index');
            $table->dropIndex('results_section_confirmed_index');
        });

        Schema::table('fixtures', function (Blueprint $table): void {
            $table->dropIndex('fixtures_section_week_date_index');
        });

        Schema::create('fixture_result_locks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('fixture_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('locked_until')->nullable();
            $table->timestamps();
        });
    }
};
