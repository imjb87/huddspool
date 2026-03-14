<?php

namespace Tests\Feature;

use App\Filament\Widgets\UserStatsOverview;
use App\Models\Fixture;
use App\Models\Frame;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Queries\GetSeasonSeriesStats;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class UserStatsOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render_user_stats_overview_widget(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        Season::factory()->create();

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(UserStatsOverview::class)
            ->assertStatus(200)
            ->assertSee('Active Players')
            ->assertSee('Matches Played')
            ->assertSee('Frames Played');
    }

    public function test_season_series_stats_use_grouped_queries(): void
    {
        Cache::flush();

        [$firstSeason, $secondSeason] = Season::factory()->count(2)->create()->all();
        $ruleset = Ruleset::factory()->create();

        $this->createSeasonResultWithFrames($firstSeason, $ruleset, 5, 4);
        $this->createSeasonResultWithFrames($secondSeason, $ruleset, 6, 3);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $series = (new GetSeasonSeriesStats)();

        $this->assertSame([$firstSeason->name, $secondSeason->name], $series['labels']);
        $this->assertSame([2, 2], $series['players']);
        $this->assertSame([1, 1], $series['results']);
        $this->assertSame([1, 1], $series['frames']);

        $queries = collect(DB::getQueryLog())->pluck('query');

        $this->assertCount(4, $queries);
        $this->assertFalse($queries->contains(fn (string $query): bool => str_contains($query, 'select distinct `home_player_id`')));
        $this->assertFalse($queries->contains(fn (string $query): bool => str_contains($query, 'select distinct `away_player_id`')));
    }

    private function createSeasonResultWithFrames(Season $season, Ruleset $ruleset, int $homeScore, int $awayScore): void
    {
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();
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
            'home_score' => $homeScore,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => $awayScore,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
        ]);

        Frame::create([
            'result_id' => $result->id,
            'home_player_id' => $homePlayer->id,
            'home_score' => 1,
            'away_player_id' => $awayPlayer->id,
            'away_score' => 0,
        ]);
    }
}
