<?php

namespace Tests\Feature;

use App\Models\Fixture;
use App\Models\Result;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CompetitionRelationshipMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_strengthening_competition_relationships_repairs_legacy_data_and_can_retry(): void
    {
        $migration = require database_path('migrations/2026_11_18_143000_strengthen_competition_relationships.php');
        $migration->down();

        $orphanFixture = Fixture::factory()->create();
        $orphanSectionId = $orphanFixture->section_id;

        DB::table('sections')
            ->where('id', $orphanSectionId)
            ->update(['season_id' => 999999]);

        DB::table('fixtures')
            ->where('id', $orphanFixture->id)
            ->update(['season_id' => 999999]);

        $validResult = Result::factory()->create();

        $orphanFrameId = DB::table('frames')->insertGetId([
            'result_id' => 999999,
            'home_player_id' => 0,
            'home_score' => 1,
            'away_player_id' => 0,
            'away_score' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $nullPlayerFrameId = DB::table('frames')->insertGetId([
            'result_id' => $validResult->id,
            'home_player_id' => 0,
            'home_score' => 1,
            'away_player_id' => 0,
            'away_score' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $validSectionId = $validResult->section_id;
        $validTeamId = $validResult->home_team_id;

        DB::table('section_team')->insert([
            [
                'section_id' => $validSectionId,
                'team_id' => $validTeamId,
                'deducted' => 0,
                'sort' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_id' => $validSectionId,
                'team_id' => $validTeamId,
                'deducted' => 0,
                'sort' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_id' => $validSectionId,
                'team_id' => 999999,
                'deducted' => 0,
                'sort' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $migration->up();

        $this->assertDatabaseMissing('sections', ['id' => $orphanSectionId]);
        $this->assertDatabaseMissing('fixtures', ['id' => $orphanFixture->id]);
        $this->assertDatabaseMissing('frames', ['id' => $orphanFrameId]);
        $this->assertDatabaseMissing('section_team', ['team_id' => 999999]);
        $this->assertSame(1, DB::table('section_team')
            ->where('section_id', $validSectionId)
            ->where('team_id', $validTeamId)
            ->count());
        $this->assertDatabaseHas('frames', [
            'id' => $nullPlayerFrameId,
            'home_player_id' => null,
            'away_player_id' => null,
        ]);
        $this->assertTrue(Schema::hasColumn('frames', 'home_player_id'));
        $this->assertTrue(Schema::hasColumn('frames', 'away_player_id'));
    }
}
