<?php

namespace Tests\Feature;

use App\Filament\Resources\SeasonResource\Pages\EditSeason;
use App\Filament\Resources\SectionResource\Pages\EditSection;
use App\Filament\Resources\SectionResource\RelationManagers\FixturesRelationManager;
use App\Models\Fixture;
use App\Models\Frame;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDeletionBehaviorTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_season_delete_action_is_disabled_when_season_has_recorded_results(): void
    {
        $admin = $this->createAdminUser();

        [
            'season' => $season,
        ] = $this->createRecordedFixtureContext();

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(EditSeason::class, [
                'record' => $season->getRouteKey(),
            ])
            ->assertActionDisabled(DeleteAction::class);

        $this->assertDatabaseHas('seasons', [
            'id' => $season->id,
        ]);
    }

    public function test_edit_section_delete_action_is_disabled_when_section_has_recorded_results(): void
    {
        $admin = $this->createAdminUser();

        [
            'section' => $section,
        ] = $this->createRecordedFixtureContext();

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(EditSection::class, [
                'record' => $section->getRouteKey(),
                'parentRecord' => $section->season,
            ])
            ->assertActionDisabled(DeleteAction::class);

        $this->assertDatabaseHas('sections', [
            'id' => $section->id,
        ]);
    }

    public function test_delete_all_fixtures_action_is_disabled_when_section_has_recorded_results(): void
    {
        $admin = $this->createAdminUser();

        [
            'section' => $section,
            'fixture' => $fixture,
            'result' => $result,
        ] = $this->createRecordedFixtureContext();

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(FixturesRelationManager::class, [
                'ownerRecord' => $section,
                'pageClass' => EditSection::class,
            ])
            ->assertActionDisabled(TestAction::make('DeleteAllFixtures')->table());

        $this->assertDatabaseHas('fixtures', [
            'id' => $fixture->id,
        ]);

        $this->assertDatabaseHas('results', [
            'id' => $result->id,
            'fixture_id' => $fixture->id,
        ]);
    }

    public function test_fixture_delete_returns_false_when_results_have_been_recorded(): void
    {
        [
            'fixture' => $fixture,
            'result' => $result,
        ] = $this->createRecordedFixtureContext();

        $this->assertFalse($fixture->delete());

        $this->assertDatabaseHas('fixtures', [
            'id' => $fixture->id,
        ]);

        $this->assertDatabaseHas('results', [
            'id' => $result->id,
            'fixture_id' => $fixture->id,
        ]);
    }

    private function createAdminUser(): User
    {
        return User::factory()->create([
            'is_admin' => true,
        ]);
    }

    /**
     * @return array{season: Season, section: Section, fixture: Fixture, result: Result}
     */
    private function createRecordedFixtureContext(): array
    {
        $season = Season::factory()->create();
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);

        $result = Result::factory()->create([
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

        Frame::create([
            'result_id' => $result->id,
            'home_player_id' => User::factory()->create(['team_id' => $homeTeam->id])->id,
            'home_score' => 1,
            'away_player_id' => User::factory()->create(['team_id' => $awayTeam->id])->id,
            'away_score' => 0,
        ]);

        return compact('season', 'section', 'fixture', 'result');
    }
}
