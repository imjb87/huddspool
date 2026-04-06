<?php

namespace Tests\Feature;

use App\Filament\Resources\SectionResource\Pages\EditSection;
use App\Filament\Resources\SectionResource\RelationManagers\TeamsRelationManager;
use App\Models\Fixture;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SectionAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_section_teams_relation_manager_shows_deducted_points(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Premier Division',
        ]);
        $homeTeam = Team::factory()->create(['name' => 'Home']);
        $awayTeam = Team::factory()->create(['name' => 'Away']);

        $section->teams()->attach($homeTeam->id, ['sort' => 1, 'deducted' => 2]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2, 'deducted' => 0]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);

        Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'home_score' => 6,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => 4,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => true,
        ]);

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(TeamsRelationManager::class, [
                'ownerRecord' => $section,
                'pageClass' => EditSection::class,
            ])
            ->assertCanSeeTableRecords([$homeTeam, $awayTeam])
            ->assertTableColumnFormattedStateSet('pivot.deducted', '-2 pts', $homeTeam)
            ->assertTableColumnFormattedStateSet('pivot.deducted', '0 pts', $awayTeam);
    }
}
