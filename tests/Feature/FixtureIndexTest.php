<?php

namespace Tests\Feature;

use App\Livewire\SectionFixtures;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FixtureIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_fixture_index_eager_loads_section_seasons(): void
    {
        $ruleset = Ruleset::factory()->create();
        $openSeason = Season::factory()->create(['is_open' => true]);
        $closedSeason = Season::factory()->create(['is_open' => false]);

        Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $openSeason->id,
        ]);

        Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $closedSeason->id,
        ]);

        $response = $this->get(route('fixture.index', $ruleset));

        $response->assertOk();
        $response->assertViewHas('sections', function ($sections) use ($openSeason): bool {
            return $sections->count() === 1
                && $sections->every(fn (Section $section): bool => $section->relationLoaded('season'))
                && $sections->first()->season->is($openSeason);
        });
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
}
