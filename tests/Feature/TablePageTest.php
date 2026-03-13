<?php

namespace Tests\Feature;

use App\Models\Fixture;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TablePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_page_displays_section_standings(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
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

        Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'home_score' => 5,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => 5,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => true,
        ]);

        $this->get(route('table.index', $ruleset))
            ->assertOk()
            ->assertSeeText('Tables')
            ->assertSeeText($ruleset->name)
            ->assertSeeText($section->name)
            ->assertSeeText($homeTeam->name)
            ->assertSeeText($awayTeam->name);
    }

    public function test_table_page_eager_loads_season_expulsions_for_sections(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $this->get(route('table.index', $ruleset))
            ->assertOk()
            ->assertViewHas('sections', function ($sections): bool {
                return $sections->isNotEmpty()
                    && $sections->every(fn (Section $section): bool => $section->relationLoaded('season'))
                    && $sections->every(fn (Section $section): bool => $section->season->relationLoaded('expulsions'));
            });
    }
}
