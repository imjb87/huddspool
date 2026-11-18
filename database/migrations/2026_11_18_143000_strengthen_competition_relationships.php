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

        $this->repairLegacyCompetitionData();

        $this->addForeignKeyIfMissing('teams', 'venue_id', 'teams_venue_id_foreign', 'venues', 'null');
        $this->addForeignKeyIfMissing('tables', 'venue_id', 'tables_venue_id_foreign', 'venues');
        $this->addForeignKeyIfMissing('sections', 'season_id', 'sections_season_id_foreign', 'seasons');
        $this->addForeignKeyIfMissing('sections', 'ruleset_id', 'sections_ruleset_id_foreign', 'rulesets');
        $this->addForeignKeyIfMissing('fixtures', 'section_id', 'fixtures_section_id_foreign', 'sections');
        $this->addForeignKeyIfMissing('fixtures', 'season_id', 'fixtures_season_id_foreign', 'seasons');
        $this->addForeignKeyIfMissing('fixtures', 'venue_id', 'fixtures_venue_id_foreign', 'venues');
        $this->addForeignKeyIfMissing('fixtures', 'ruleset_id', 'fixtures_ruleset_id_foreign', 'rulesets', 'null');
        $this->addForeignKeyIfMissing('results', 'fixture_id', 'results_fixture_id_foreign', 'fixtures');
        $this->addForeignKeyIfMissing('results', 'submitted_by', 'results_submitted_by_foreign', 'users', 'null');
        $this->addForeignKeyIfMissing('frames', 'result_id', 'frames_result_id_foreign', 'results', 'cascade');
        $this->addForeignKeyIfMissing('frames', 'home_player_id', 'frames_home_player_id_foreign', 'users');
        $this->addForeignKeyIfMissing('frames', 'away_player_id', 'frames_away_player_id_foreign', 'users');
        $this->addUniqueIndexIfMissing('section_team', ['section_id', 'team_id'], 'section_team_section_id_team_id_unique');
        $this->addForeignKeyIfMissing('section_team', 'section_id', 'section_team_section_id_foreign', 'sections', 'cascade');
        $this->addForeignKeyIfMissing('section_team', 'team_id', 'section_team_team_id_foreign', 'teams', 'cascade');
    }

    private function repairLegacyCompetitionData(): void
    {
        Schema::table('frames', function (Blueprint $table): void {
            $table->unsignedBigInteger('home_player_id')->nullable()->change();
            $table->unsignedBigInteger('away_player_id')->nullable()->change();
        });

        DB::table('frames')
            ->where('home_player_id', 0)
            ->update(['home_player_id' => null]);

        DB::table('frames')
            ->where('away_player_id', 0)
            ->update(['away_player_id' => null]);

        $missingSubmittedByIds = DB::table('results as results')
            ->leftJoin('users', 'users.id', '=', 'results.submitted_by')
            ->whereNotNull('results.submitted_by')
            ->whereNull('users.id')
            ->pluck('results.id');

        if ($missingSubmittedByIds->isNotEmpty()) {
            DB::table('results')
                ->whereIn('id', $missingSubmittedByIds)
                ->update(['submitted_by' => null]);
        }

        $orphanSectionIds = DB::table('sections as sections')
            ->leftJoin('seasons', 'seasons.id', '=', 'sections.season_id')
            ->whereNull('seasons.id')
            ->pluck('sections.id');

        if ($orphanSectionIds->isNotEmpty()) {
            $sectionFixtureIds = DB::table('fixtures')
                ->whereIn('section_id', $orphanSectionIds)
                ->pluck('id');

            if ($sectionFixtureIds->isNotEmpty() &&
                DB::table('results')->whereIn('fixture_id', $sectionFixtureIds)->exists()) {
                throw new RuntimeException('Cannot remove orphaned sections because recorded results reference their fixtures.');
            }

            DB::table('section_team')
                ->whereIn('section_id', $orphanSectionIds)
                ->delete();

            DB::table('fixtures')
                ->whereIn('id', $sectionFixtureIds)
                ->delete();

            DB::table('sections')
                ->whereIn('id', $orphanSectionIds)
                ->delete();
        }

        $orphanSectionFixtureIds = DB::table('fixtures as fixtures')
            ->leftJoin('sections', 'sections.id', '=', 'fixtures.section_id')
            ->whereNotNull('fixtures.section_id')
            ->whereNull('sections.id')
            ->pluck('fixtures.id');

        $orphanSeasonFixtureIds = DB::table('fixtures as fixtures')
            ->leftJoin('seasons', 'seasons.id', '=', 'fixtures.season_id')
            ->whereNotNull('fixtures.season_id')
            ->whereNull('seasons.id')
            ->pluck('fixtures.id');

        $orphanFixtureIds = $orphanSectionFixtureIds
            ->merge($orphanSeasonFixtureIds)
            ->unique()
            ->values();

        if ($orphanFixtureIds->isNotEmpty()) {
            if (DB::table('results')->whereIn('fixture_id', $orphanFixtureIds)->exists()) {
                throw new RuntimeException('Cannot remove orphaned fixtures because recorded results reference them.');
            }

            DB::table('fixtures')
                ->whereIn('id', $orphanFixtureIds)
                ->delete();
        }

        $orphanResultIds = DB::table('results as results')
            ->leftJoin('fixtures', 'fixtures.id', '=', 'results.fixture_id')
            ->whereNull('fixtures.id')
            ->pluck('results.id');

        if ($orphanResultIds->isNotEmpty()) {
            DB::table('frames')
                ->whereIn('result_id', $orphanResultIds)
                ->delete();

            DB::table('results')
                ->whereIn('id', $orphanResultIds)
                ->delete();
        }

        $orphanFrameIds = DB::table('frames as frames')
            ->leftJoin('results', 'results.id', '=', 'frames.result_id')
            ->whereNull('results.id')
            ->pluck('frames.id');

        if ($orphanFrameIds->isNotEmpty()) {
            DB::table('frames')
                ->whereIn('id', $orphanFrameIds)
                ->delete();
        }

        $missingHomePlayerFrameIds = DB::table('frames as frames')
            ->leftJoin('users', 'users.id', '=', 'frames.home_player_id')
            ->whereNotNull('frames.home_player_id')
            ->whereNull('users.id')
            ->pluck('frames.id');

        if ($missingHomePlayerFrameIds->isNotEmpty()) {
            DB::table('frames')
                ->whereIn('id', $missingHomePlayerFrameIds)
                ->update(['home_player_id' => null]);
        }

        $missingAwayPlayerFrameIds = DB::table('frames as frames')
            ->leftJoin('users', 'users.id', '=', 'frames.away_player_id')
            ->whereNotNull('frames.away_player_id')
            ->whereNull('users.id')
            ->pluck('frames.id');

        if ($missingAwayPlayerFrameIds->isNotEmpty()) {
            DB::table('frames')
                ->whereIn('id', $missingAwayPlayerFrameIds)
                ->update(['away_player_id' => null]);
        }

        $invalidSectionTeamIds = DB::table('section_team as section_team')
            ->leftJoin('sections', 'sections.id', '=', 'section_team.section_id')
            ->leftJoin('teams', 'teams.id', '=', 'section_team.team_id')
            ->where(function ($query): void {
                $query
                    ->whereNull('sections.id')
                    ->orWhereNull('teams.id');
            })
            ->pluck('section_team.id');

        if ($invalidSectionTeamIds->isNotEmpty()) {
            DB::table('section_team')
                ->whereIn('id', $invalidSectionTeamIds)
                ->delete();
        }

        $duplicateSectionTeams = DB::table('section_team')
            ->select('section_id', 'team_id')
            ->groupBy('section_id', 'team_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicateSectionTeams as $duplicateSectionTeam) {
            $duplicateIds = DB::table('section_team')
                ->where('section_id', $duplicateSectionTeam->section_id)
                ->where('team_id', $duplicateSectionTeam->team_id)
                ->orderBy('id')
                ->pluck('id');

            DB::table('section_team')
                ->whereIn('id', $duplicateIds->skip(1))
                ->delete();
        }
    }

    private function addForeignKeyIfMissing(
        string $tableName,
        string $columnName,
        string $constraintName,
        string $referencedTable,
        string $onDelete = 'restrict',
    ): void {
        if ($this->foreignKeyExists($tableName, $columnName, $referencedTable)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($columnName, $constraintName, $referencedTable, $onDelete): void {
            $foreignKey = $table->foreign($columnName, $constraintName)
                ->references('id')
                ->on($referencedTable);

            if ($onDelete === 'cascade') {
                $foreignKey->cascadeOnDelete();
            } elseif ($onDelete === 'null') {
                $foreignKey->nullOnDelete();
            } else {
                $foreignKey->restrictOnDelete();
            }
        });
    }

    private function foreignKeyExists(string $tableName, string $columnName, string $referencedTable): bool
    {
        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('CONSTRAINT_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', $tableName)
            ->where('COLUMN_NAME', $columnName)
            ->where('REFERENCED_TABLE_NAME', $referencedTable)
            ->where('REFERENCED_COLUMN_NAME', 'id')
            ->exists();
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function addUniqueIndexIfMissing(string $tableName, array $columns, string $indexName): void
    {
        if ($this->uniqueIndexExists($tableName, $indexName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName): void {
            $table->unique($columns, $indexName);
        });
    }

    private function uniqueIndexExists(string $tableName, string $indexName): bool
    {
        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', $tableName)
            ->where('INDEX_NAME', $indexName)
            ->where('NON_UNIQUE', 0)
            ->exists();
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
