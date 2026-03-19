<?php

namespace Tests\Feature;

use App\Models\Fixture;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

        $this->get(route('ruleset.section.show', [
            'ruleset' => $ruleset,
            'section' => $section,
        ]))
            ->assertOk()
            ->assertSeeText('Standings')
            ->assertSeeText($ruleset->name)
            ->assertSeeText($section->name)
            ->assertSeeText($homeTeam->name)
            ->assertSeeText($awayTeam->name);
    }

    public function test_legacy_table_page_redirects_to_the_canonical_ruleset_hub(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $this->get(route('table.index', $ruleset))
            ->assertRedirect(route('ruleset.section.show', [
                'ruleset' => $ruleset,
                'section' => $section,
            ]));
    }

    public function test_table_page_does_not_issue_per_section_result_queries_when_building_standings(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();

        foreach (range(1, 2) as $index) {
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
                'home_score' => 5 + $index,
                'away_team_id' => $awayTeam->id,
                'away_team_name' => $awayTeam->name,
                'away_score' => 4,
                'section_id' => $section->id,
                'ruleset_id' => $ruleset->id,
                'is_confirmed' => true,
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $targetSection = Section::query()->where('ruleset_id', $ruleset->id)->orderBy('name')->first();

        $this->get(route('ruleset.section.show', [
            'ruleset' => $ruleset,
            'section' => $targetSection,
        ]))
            ->assertOk();

        $fallbackQueries = collect(DB::getQueryLog())
            ->pluck('query')
            ->filter(fn (string $query): bool => str_contains($query, 'from `results` inner join `fixtures`'))
            ->filter(fn (string $query): bool => str_contains($query, 'where `fixtures`.`section_id` = ?'))
            ->filter(fn (string $query): bool => str_contains($query, '`is_confirmed` = ?'));

        $this->assertCount(0, $fallbackQueries);
    }
}
