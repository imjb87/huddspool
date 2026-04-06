<?php

namespace Tests\Feature;

use App\Filament\Resources\SectionResource\Pages\PreviewFixtures;
use App\Filament\Resources\VenueResource\Pages\EditVenue;
use App\Filament\Resources\VenueResource\RelationManagers\TeamsRelationManager;
use App\KnockoutType;
use App\Models\Fixture;
use App\Models\Knockout;
use App\Models\KnockoutParticipant;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use App\Support\KnockoutMatchVenueOptions;
use App\Support\SectionFixturePreviewBuilder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentAdminRelationRefactorTest extends TestCase
{
    use RefreshDatabase;

    public function test_venue_teams_relation_manager_dissociates_teams_from_a_venue(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $venue = Venue::factory()->create();
        $team = Team::factory()->create([
            'venue_id' => $venue->id,
        ]);

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(TeamsRelationManager::class, [
                'ownerRecord' => $venue,
                'pageClass' => EditVenue::class,
            ])
            ->callTableAction('dissociate', (string) $team->getKey())
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'venue_id' => null,
        ]);
    }

    public function test_section_fixture_preview_builder_marks_existing_venue_conflicts(): void
    {
        $fixtureDate = now()->addWeek()->toDateString();
        $season = Season::factory()->create([
            'dates' => [$fixtureDate],
        ]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $conflictingSection = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Existing clashes',
        ]);

        $teams = Team::factory()->count(10)->create();
        foreach ($teams as $index => $team) {
            $section->teams()->attach($team->id, ['sort' => $index + 1]);
        }

        $homeTeam = $teams->last();
        $awayTeam = $teams->first();

        Fixture::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'section_id' => $conflictingSection->id,
            'fixture_date' => $fixtureDate,
            'venue_id' => $homeTeam->venue_id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);

        $preview = app(SectionFixturePreviewBuilder::class)->build($section);

        $conflictedFixture = collect($preview)->firstWhere('has_conflict', true);

        $this->assertNotNull($conflictedFixture);
        $this->assertSame('Existing clashes', $conflictedFixture['conflicts'][0]['section']);
    }

    public function test_section_fixture_preview_page_groups_fixtures_and_shows_conflicts(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $fixtureDate = now()->addWeek()->toDateString();
        $season = Season::factory()->create([
            'dates' => collect(range(0, 17))
                ->map(fn (int $week): string => now()->addWeek()->addWeeks($week)->toDateString())
                ->all(),
        ]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Preview Section',
        ]);
        $conflictingSection = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Existing clashes',
        ]);

        $teams = Team::factory()->count(10)->create();
        foreach ($teams as $index => $team) {
            $section->teams()->attach($team->id, ['sort' => $index + 1]);
        }

        Fixture::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'section_id' => $conflictingSection->id,
            'fixture_date' => $fixtureDate,
            'venue_id' => $teams->last()->venue_id,
            'home_team_id' => $teams->last()->id,
            'away_team_id' => $teams->first()->id,
        ]);

        Filament::setCurrentPanel('admin');

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.seasons.sections.preview-fixtures', [
                'season' => $season,
                'record' => $section,
            ]))
            ->assertOk()
            ->assertSee('Week 1')
            ->assertSee('Venue clash')
            ->assertSee('Existing clashes')
            ->assertSee('Create fixtures');
    }

    public function test_section_fixture_preview_page_is_hidden_once_fixtures_exist(): void
    {
        $season = Season::factory()->create([
            'dates' => collect(range(0, 17))
                ->map(fn (int $week): string => now()->addWeek()->addWeeks($week)->toDateString())
                ->all(),
        ]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $teams = Team::factory()->count(10)->create();
        foreach ($teams as $index => $team) {
            $section->teams()->attach($team->id, ['sort' => $index + 1]);
        }

        $section->generateFixtures();

        $this->assertFalse(PreviewFixtures::shouldRegisterNavigation([
            'record' => $section,
            'season' => $season,
        ]));

        $this->assertFalse(PreviewFixtures::canAccess([
            'record' => $section,
            'season' => $season,
        ]));
    }

    public function test_knockout_match_form_schema_excludes_participant_venues_from_neutral_venue_options(): void
    {
        $homeVenue = Venue::factory()->create([
            'name' => 'Home Venue',
            'latitude' => 53.645,
            'longitude' => -1.784,
        ]);
        $awayVenue = Venue::factory()->create([
            'name' => 'Away Venue',
            'latitude' => 53.700,
            'longitude' => -1.800,
        ]);
        $neutralVenue = Venue::factory()->create([
            'name' => 'Neutral Venue',
            'latitude' => 53.670,
            'longitude' => -1.790,
        ]);
        $homeTeam = Team::factory()->create(['venue_id' => $homeVenue->id]);
        $awayTeam = Team::factory()->create(['venue_id' => $awayVenue->id]);
        $homePlayer = User::factory()->create(['team_id' => $homeTeam->id]);
        $awayPlayer = User::factory()->create(['team_id' => $awayTeam->id]);
        $knockout = Knockout::factory()->create([
            'type' => KnockoutType::Singles,
        ]);
        $homeParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $homePlayer->id,
            'seed' => 1,
        ]);
        $awayParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $awayPlayer->id,
            'seed' => 2,
        ]);

        $options = app(KnockoutMatchVenueOptions::class)->venueOptions(
            $knockout,
            $homeParticipant->id,
            $awayParticipant->id,
            null,
        );

        $this->assertArrayNotHasKey($homeVenue->id, $options);
        $this->assertArrayNotHasKey($awayVenue->id, $options);
        $this->assertArrayHasKey($neutralVenue->id, $options);
        $this->assertStringContainsString('km from neutral point', $options[$neutralVenue->id]);
    }
}
