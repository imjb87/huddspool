<?php

namespace Tests\Feature;

use App\Filament\Resources\SectionResource\Pages\EditSection;
use App\Filament\Resources\SectionResource\RelationManagers\TeamsRelationManager;
use App\Models\Fixture;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\SectionTeam;
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
        $homeSectionTeam = SectionTeam::query()
            ->where('section_id', $section->id)
            ->where('team_id', $homeTeam->id)
            ->firstOrFail();
        $awaySectionTeam = SectionTeam::query()
            ->where('section_id', $section->id)
            ->where('team_id', $awayTeam->id)
            ->firstOrFail();

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(TeamsRelationManager::class, [
                'ownerRecord' => $section,
                'pageClass' => EditSection::class,
            ])
            ->assertCanSeeTableRecords([$homeSectionTeam, $awaySectionTeam])
            ->assertTableColumnFormattedStateSet('deducted', '-2 pts', $homeSectionTeam)
            ->assertTableColumnFormattedStateSet('deducted', '0 pts', $awaySectionTeam)
            ->mountTableAction('DeductPoints', (string) $homeSectionTeam->getKey())
            ->assertTableActionDataSet([
                'deducted' => 2,
            ])
            ->setTableActionData([
                'deducted' => 3,
            ])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('section_team', [
            'id' => $homeSectionTeam->getKey(),
            'section_id' => $section->id,
            'team_id' => $homeTeam->id,
            'deducted' => 3,
        ]);
    }

    public function test_section_teams_relation_manager_can_add_multiple_existing_teams(): void
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
        $existingTeam = Team::factory()->create(['name' => 'Existing']);
        $newTeam = Team::factory()->create(['name' => 'New Team']);
        $anotherTeam = Team::factory()->create(['name' => 'Another Team']);

        $section->teams()->attach($existingTeam->id, ['sort' => 1, 'deducted' => 0]);

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(TeamsRelationManager::class, [
                'ownerRecord' => $section,
                'pageClass' => EditSection::class,
            ])
            ->callTableAction('AddExistingTeam', data: [
                'team_ids' => [$newTeam->id, $anotherTeam->id],
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('section_team', [
            'section_id' => $section->id,
            'team_id' => $newTeam->id,
            'sort' => 2,
            'deducted' => 0,
        ]);

        $this->assertDatabaseHas('section_team', [
            'section_id' => $section->id,
            'team_id' => $anotherTeam->id,
            'sort' => 3,
            'deducted' => 0,
        ]);
    }
}
