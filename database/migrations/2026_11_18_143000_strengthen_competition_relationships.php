<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table): void {
            $table->unsignedBigInteger('submitted_by')->nullable()->default(null)->change();
        });

        DB::table('results')
            ->where('submitted_by', 0)
            ->update(['submitted_by' => null]);

        Schema::table('teams', function (Blueprint $table): void {
            $table->foreign('venue_id', 'teams_venue_id_foreign')
                ->references('id')
                ->on('venues')
                ->nullOnDelete();
        });

        Schema::table('tables', function (Blueprint $table): void {
            $table->foreign('venue_id', 'tables_venue_id_foreign')
                ->references('id')
                ->on('venues')
                ->restrictOnDelete();
        });

        Schema::table('sections', function (Blueprint $table): void {
            $table->foreign('season_id', 'sections_season_id_foreign')
                ->references('id')
                ->on('seasons')
                ->restrictOnDelete();
            $table->foreign('ruleset_id', 'sections_ruleset_id_foreign')
                ->references('id')
                ->on('rulesets')
                ->restrictOnDelete();
        });

        Schema::table('fixtures', function (Blueprint $table): void {
            $table->foreign('section_id', 'fixtures_section_id_foreign')
                ->references('id')
                ->on('sections')
                ->restrictOnDelete();
            $table->foreign('season_id', 'fixtures_season_id_foreign')
                ->references('id')
                ->on('seasons')
                ->restrictOnDelete();
            $table->foreign('venue_id', 'fixtures_venue_id_foreign')
                ->references('id')
                ->on('venues')
                ->restrictOnDelete();
            $table->foreign('ruleset_id', 'fixtures_ruleset_id_foreign')
                ->references('id')
                ->on('rulesets')
                ->nullOnDelete();
        });

        Schema::table('results', function (Blueprint $table): void {
            $table->foreign('fixture_id', 'results_fixture_id_foreign')
                ->references('id')
                ->on('fixtures')
                ->restrictOnDelete();
            $table->foreign('submitted_by', 'results_submitted_by_foreign')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });

        Schema::table('frames', function (Blueprint $table): void {
            $table->foreign('result_id', 'frames_result_id_foreign')
                ->references('id')
                ->on('results')
                ->cascadeOnDelete();
            $table->foreign('home_player_id', 'frames_home_player_id_foreign')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
            $table->foreign('away_player_id', 'frames_away_player_id_foreign')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
        });

        Schema::table('section_team', function (Blueprint $table): void {
            $table->unique(['section_id', 'team_id'], 'section_team_section_id_team_id_unique');
            $table->foreign('section_id', 'section_team_section_id_foreign')
                ->references('id')
                ->on('sections')
                ->cascadeOnDelete();
            $table->foreign('team_id', 'section_team_team_id_foreign')
                ->references('id')
                ->on('teams')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('section_team', function (Blueprint $table): void {
            $table->dropForeign('section_team_section_id_foreign');
            $table->dropForeign('section_team_team_id_foreign');
            $table->dropUnique('section_team_section_id_team_id_unique');
        });

        Schema::table('frames', function (Blueprint $table): void {
            $table->dropForeign('frames_result_id_foreign');
            $table->dropForeign('frames_home_player_id_foreign');
            $table->dropForeign('frames_away_player_id_foreign');
        });

        Schema::table('results', function (Blueprint $table): void {
            $table->dropForeign('results_fixture_id_foreign');
            $table->dropForeign('results_submitted_by_foreign');
        });

        Schema::table('fixtures', function (Blueprint $table): void {
            $table->dropForeign('fixtures_section_id_foreign');
            $table->dropForeign('fixtures_season_id_foreign');
            $table->dropForeign('fixtures_venue_id_foreign');
            $table->dropForeign('fixtures_ruleset_id_foreign');
        });

        Schema::table('sections', function (Blueprint $table): void {
            $table->dropForeign('sections_season_id_foreign');
            $table->dropForeign('sections_ruleset_id_foreign');
        });

        Schema::table('tables', function (Blueprint $table): void {
            $table->dropForeign('tables_venue_id_foreign');
        });

        Schema::table('teams', function (Blueprint $table): void {
            $table->dropForeign('teams_venue_id_foreign');
        });

        DB::table('results')
            ->whereNull('submitted_by')
            ->update(['submitted_by' => 0]);

        Schema::table('results', function (Blueprint $table): void {
            $table->integer('submitted_by')->default(0)->nullable(false)->change();
        });
    }
};
