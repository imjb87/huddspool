<?php

namespace Tests\Feature;

use App\Models\Fixture;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FixtureShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_fixture_show_displays_team_players(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        Team::factory()->create();

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);

        $homePlayer = User::factory()->create(['team_id' => $homeTeam->id]);
        $awayPlayer = User::factory()->create(['team_id' => $awayTeam->id]);

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
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $homePlayer->id,
        ]);

        $this->actingAs($homePlayer);

        $response = $this->get(route('fixture.show', $fixture));

        $response->assertStatus(200);
        $response->assertSee('data-fixture-page', false);
        $response->assertSee('data-fixture-info-section', false);
        $response->assertSee('data-fixture-head-to-head-section', false);
        $response->assertSee('data-fixture-home-team-section', false);
        $response->assertSee('data-fixture-away-team-section', false);
        $response->assertSee('dark:bg-zinc-900', false);
        $response->assertSee('dark:border-zinc-800/80', false);
        $response->assertSee('dark:text-gray-100', false);
        $response->assertSeeText('Fixture information');
        $response->assertSeeText('Head to head');
        $response->assertSeeTextInOrder([$homeTeam->name, 'vs', $awayTeam->name]);
        $response->assertSeeText($homePlayer->name);
        $response->assertSeeText($awayPlayer->name);
        $response->assertSeeText('Played');
        $response->assertSeeText('Won');
        $response->assertSeeText('Lost');
        $response->assertSeeText('0%');
    }

    public function test_fixture_show_returns_not_found_when_a_team_relation_is_missing(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        Team::factory()->create();

        $homeTeam = Team::factory()->create();

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => 999999,
            'venue_id' => $homeTeam->venue_id,
        ]);

        $response = $this->get(route('fixture.show', $fixture));

        $response->assertNotFound();
    }

    public function test_fixture_show_returns_not_found_for_a_bye_fixture(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $homeTeam = Team::factory()->create(['name' => 'Bye']);
        $awayTeam = Team::factory()->create(['name' => 'Blues']);

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);

        $homePlayer = User::factory()->create(['team_id' => $homeTeam->id]);
        User::factory()->create(['team_id' => $awayTeam->id]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);

        $this->actingAs($homePlayer);

        $this->get(route('fixture.show', $fixture))
            ->assertNotFound();
    }
}
