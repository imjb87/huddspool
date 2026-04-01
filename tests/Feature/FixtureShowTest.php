<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Livewire\Fixture\TeamSection as FixtureTeamSection;
use App\Models\Fixture;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

        $homePlayer = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => UserRole::TeamAdmin->value,
        ]);
        $awayPlayer = User::factory()->create([
            'team_id' => $awayTeam->id,
            'role' => UserRole::Player->value,
        ]);

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
        $response->assertSee('ui-page-shell', false);
        $response->assertSee('data-section-shared-header', false);
        $response->assertSee('data-fixture-info-section', false);
        $response->assertSee('data-fixture-head-to-head-section', false);
        $response->assertSee('data-fixture-home-team-section', false);
        $response->assertSee('data-fixture-away-team-section', false);
        $response->assertSeeLivewire(FixtureTeamSection::class);
        $response->assertSee('ui-shell-grid', false);
        $response->assertSee('ui-card', false);
        $response->assertSee('ui-card-rows', false);
        $response->assertSee('dark:bg-neutral-950', false);
        $response->assertSee('dark:border-neutral-800/80', false);
        $response->assertSee('dark:text-gray-100', false);
        $response->assertSeeText('Fixture');
        $response->assertSeeText('Fixture information');
        $response->assertSeeText('Head to head');
        $response->assertSeeTextInOrder([$homeTeam->name, 'vs', $awayTeam->name]);
        $response->assertSee('href="'.route('team.show', $homeTeam).'"', false);
        $response->assertSee('href="'.route('team.show', $awayTeam).'"', false);
        $response->assertSeeText($homePlayer->name);
        $response->assertSeeText($awayPlayer->name);
        $response->assertSeeText(UserRole::labelFor($homePlayer->role));
        $response->assertSeeText(UserRole::labelFor($awayPlayer->role));
        $response->assertSeeText('Played');
        $response->assertSeeText('Won');
        $response->assertSeeText('Lost');
        $response->assertSeeText('0%');
    }

    public function test_fixture_show_team_sections_paginate_players_by_five(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        Team::factory()->create();

        $homeTeam = Team::factory()->create(['name' => 'Home Team']);
        $awayTeam = Team::factory()->create(['name' => 'Away Team']);

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);

        foreach (range(1, 6) as $index) {
            User::factory()->create([
                'team_id' => $homeTeam->id,
                'name' => sprintf('Home Player %02d', $index),
                'role' => UserRole::Player->value,
            ]);

            User::factory()->create([
                'team_id' => $awayTeam->id,
                'name' => sprintf('Away Player %02d', $index),
                'role' => UserRole::Player->value,
            ]);
        }

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);

        $this->get(route('fixture.show', $fixture))
            ->assertOk()
            ->assertSee('data-fixture-home-team-section-controls', false)
            ->assertSee('data-fixture-away-team-section-controls', false);

        Livewire::test(FixtureTeamSection::class, [
            'team' => $homeTeam,
            'section' => $section,
            'title' => $homeTeam->name,
            'sectionKey' => 'fixture-home-team-section',
            'side' => 'home',
        ])
            ->assertSee('Page 1')
            ->assertSee('Home Player 01')
            ->assertSee('Home Player 05')
            ->assertDontSee('Home Player 06')
            ->call('nextPage')
            ->assertSee('Page 2')
            ->assertSee('Home Player 06')
            ->assertDontSee('Home Player 01');

        Livewire::test(FixtureTeamSection::class, [
            'team' => $awayTeam,
            'section' => $section,
            'title' => $awayTeam->name,
            'sectionKey' => 'fixture-away-team-section',
            'side' => 'away',
        ])
            ->assertSee('Page 1')
            ->assertSee('Away Player 01')
            ->assertSee('Away Player 05')
            ->assertDontSee('Away Player 06')
            ->call('nextPage')
            ->assertSee('Page 2')
            ->assertSee('Away Player 06')
            ->assertDontSee('Away Player 01');
    }

    public function test_fixture_show_uses_actual_section_positions_in_head_to_head(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        Team::factory()->create();

        $topTeam = Team::factory()->create(['name' => 'Leaders']);
        $homeTeam = Team::factory()->create(['name' => 'Home Team']);
        $awayTeam = Team::factory()->create(['name' => 'Away Team']);

        $section->teams()->attach($topTeam->id, ['sort' => 1]);
        $section->teams()->attach($homeTeam->id, ['sort' => 2]);
        $section->teams()->attach($awayTeam->id, ['sort' => 3]);

        $homePlayer = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => UserRole::TeamAdmin->value,
        ]);

        Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $topTeam->id,
            'away_team_id' => $homeTeam->id,
        ]);

        Result::factory()->create([
            'home_team_id' => $topTeam->id,
            'home_team_name' => $topTeam->name,
            'away_team_id' => $homeTeam->id,
            'away_team_name' => $homeTeam->name,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_score' => 7,
            'away_score' => 2,
            'submitted_by' => $homePlayer->id,
        ]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);

        $this->actingAs($homePlayer);

        $response = $this->get(route('fixture.show', $fixture));
        $content = $response->getContent();

        $response->assertOk();
        $this->assertIsString($content);
        $this->assertMatchesRegularExpression('/<div class="w-8 shrink-0 text-sm font-semibold text-gray-500 dark:text-gray-400">\s*2\s*<\/div>[\s\S]*?Home Team/', $content);
        $this->assertMatchesRegularExpression('/<div class="w-8 shrink-0 text-sm font-semibold text-gray-500 dark:text-gray-400">\s*3\s*<\/div>[\s\S]*?Away Team/', $content);
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
