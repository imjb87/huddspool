<?php

namespace Tests\Unit;

use App\Models\Fixture;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SectionFixtureGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_fixtures_creates_a_full_schedule_for_a_section(): void
    {
        $season = Season::factory()->create([
            'dates' => collect(range(0, 17))
                ->map(fn (int $week): string => Carbon::create(2026, 1, 6)->addWeeks($week)->toDateString())
                ->all(),
        ]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $teams = Team::factory()->count(10)->create();

        $teams->each(function (Team $team, int $index) use ($section): void {
            $section->teams()->attach($team->id, ['sort' => $index + 1]);
        });

        $section->generateFixtures();

        $fixtures = Fixture::query()
            ->where('section_id', $section->id)
            ->orderBy('week')
            ->get();

        $teamIds = $teams->pluck('id')->all();
        $venueIdsByTeam = $teams->pluck('venue_id', 'id')->all();

        $this->assertCount(90, $fixtures);
        $this->assertCount(5, $fixtures->where('week', 1));
        $this->assertCount(5, $fixtures->where('week', 18));
        $this->assertTrue($fixtures->every(function (Fixture $fixture) use ($season, $ruleset, $section, $teamIds, $venueIdsByTeam): bool {
            return $fixture->season_id === $season->id
                && $fixture->ruleset_id === $ruleset->id
                && $fixture->section_id === $section->id
                && in_array($fixture->home_team_id, $teamIds, true)
                && in_array($fixture->away_team_id, $teamIds, true)
                && $fixture->venue_id === $venueIdsByTeam[$fixture->home_team_id];
        }));
        $this->assertTrue($fixtures->where('week', 1)->every(
            fn (Fixture $fixture): bool => $fixture->fixture_date?->toDateString() === '2026-01-06'
        ));
    }
}
