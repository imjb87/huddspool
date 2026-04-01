<?php

namespace Tests\Feature;

use App\Livewire\SectionFixtures;
use App\Models\Fixture;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class FixtureIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_fixtures_results_tab_uses_the_canonical_ruleset_hub_route(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $this->assertSame(
            "/rulesets/{$ruleset->slug}/{$section->slug}?tab=fixtures-results",
            route('ruleset.section.show', [
                'ruleset' => $ruleset,
                'section' => $section,
                'tab' => 'fixtures-results',
            ], false)
        );
    }

    public function test_section_fixtures_eager_loads_fixture_relations(): void
    {
        $section = null;

        Model::withoutEvents(function () use (&$section): void {
            $season = Season::factory()->create([
                'is_open' => true,
                'dates' => [now()->toDateString()],
            ]);
            $ruleset = Ruleset::factory()->create();
            $section = Section::factory()->create([
                'season_id' => $season->id,
                'ruleset_id' => $ruleset->id,
            ]);

            Team::factory()->create();

            $homeTeam = Team::factory()->create();
            $awayTeam = Team::factory()->create();

            $fixture = $section->fixtures()->create([
                'week' => 1,
                'fixture_date' => now()->toDateString(),
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'season_id' => $season->id,
                'venue_id' => $homeTeam->venue_id,
                'ruleset_id' => $ruleset->id,
            ]);

            Result::factory()->create([
                'fixture_id' => $fixture->id,
                'home_team_id' => $homeTeam->id,
                'home_team_name' => $homeTeam->name,
                'away_team_id' => $awayTeam->id,
                'away_team_name' => $awayTeam->name,
                'section_id' => $section->id,
                'ruleset_id' => $ruleset->id,
            ]);
        });

        Livewire::test(SectionFixtures::class, ['section' => $section])
            ->assertViewHas('fixtures', function ($fixtures): bool {
                return $fixtures->count() === 1
                    && $fixtures->every(fn ($fixture): bool => $fixture->relationLoaded('result'))
                    && $fixtures->every(fn ($fixture): bool => $fixture->relationLoaded('homeTeam'))
                    && $fixtures->every(fn ($fixture): bool => $fixture->relationLoaded('awayTeam'));
            });
    }

    public function test_fixture_index_does_not_lazy_load_the_authenticated_users_team_for_navigation(): void
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

        Result::factory()->create([
            'fixture_id' => $section->fixtures()->create([
                'week' => 1,
                'fixture_date' => now()->toDateString(),
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'season_id' => $season->id,
                'venue_id' => $homeTeam->venue_id,
                'ruleset_id' => $ruleset->id,
            ])->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $user = User::factory()->create([
            'team_id' => $homeTeam->id,
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->actingAs($user)
            ->get(route('ruleset.section.show', [
                'ruleset' => $ruleset,
                'section' => $section,
                'tab' => 'fixtures-results',
            ]))
            ->assertOk();

        $teamQueries = collect(DB::getQueryLog())
            ->pluck('query')
            ->filter(fn (string $query): bool => str_contains($query, 'from `teams`'))
            ->values();

        $this->assertFalse(
            $teamQueries->contains(fn (string $query): bool => str_contains($query, '`deleted_at` is null limit 1')),
            'The fixtures index route lazily loaded the authenticated user team relation.'
        );
    }

    public function test_fixtures_results_page_renders_fixed_width_gradient_score_pills(): void
    {
        $season = Season::factory()->create([
            'is_open' => true,
            'dates' => [now()->toDateString()],
        ]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $fixture = Fixture::factory()->create([
            'week' => 1,
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'fixture_date' => now(),
        ]);

        Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'home_score' => 10,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => 8,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => true,
        ]);

        $this->get(route('ruleset.section.show', [
            'ruleset' => $ruleset,
            'section' => $section,
            'tab' => 'fixtures-results',
        ]))
            ->assertOk()
            ->assertSee('data-section-fixtures-score-pill', false)
            ->assertSee('ui-score-pill ui-score-pill-neutral ui-score-pill-split', false)
            ->assertSee('ui-score-pill-segment', false)
            ->assertSee('truncate text-sm font-semibold', false)
            ->assertSee('ui-score-pill-divider-neutral', false)
            ->assertSeeText('10')
            ->assertSeeText('8');
    }
}
