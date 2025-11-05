<?php

namespace Tests\Feature;

use App\Livewire\HomeLatestActivity;
use App\Models\Fixture;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class HomeLatestActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_displays_updates_fixtures_and_standings(): void
    {
        Carbon::setTestNow(Carbon::now()->setHour(18)->setMinute(0));

        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();
        $thirdTeam = Team::factory()->create();

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);
        $section->teams()->attach($thirdTeam->id, ['sort' => 3]);

        $venue = Venue::factory()->create();

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'fixture_date' => Carbon::today()->setHour(20),
            'venue_id' => $venue->id,
        ]);

        $teamAdmin = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 2,
            'is_admin' => false,
        ]);

        $partialResult = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'home_score' => 3,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => 2,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $teamAdmin->id,
            'is_confirmed' => false,
        ]);
        $partialResult->update(['updated_at' => Carbon::now()->subMinutes(5)]);

        // Create an additional confirmed result to populate standings
        $otherFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $thirdTeam->id,
            'away_team_id' => $homeTeam->id,
            'fixture_date' => Carbon::yesterday()->setHour(20),
            'venue_id' => $venue->id,
        ]);

        $confirmedResult = Result::factory()->create([
            'fixture_id' => $otherFixture->id,
            'home_team_id' => $thirdTeam->id,
            'home_team_name' => $thirdTeam->name,
            'home_score' => 5,
            'away_team_id' => $homeTeam->id,
            'away_team_name' => $homeTeam->name,
            'away_score' => 5,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $teamAdmin->id,
            'is_confirmed' => true,
        ]);
        $confirmedResult->update(['updated_at' => Carbon::now()->subDay()]);

        Livewire::actingAs($teamAdmin)
            ->test(HomeLatestActivity::class)
            ->assertSee('Latest activity')
            ->assertSee('Resume')
            ->assertSee('Final');

        Carbon::setTestNow();
    }
}
