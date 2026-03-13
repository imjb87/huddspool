<?php

namespace Tests\Feature;

use App\Filament\Widgets\SeasonStatsChart;
use App\Models\Fixture;
use App\Models\Frame;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class SeasonStatsChartTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render_season_stats_chart_widget(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        Season::factory()->create();

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(SeasonStatsChart::class)
            ->assertStatus(200)
            ->assertSee('Season trends');
    }

    public function test_result_changes_clear_chart_cache(): void
    {
        Cache::put('stats:season-series-chart', ['cached' => true], now()->addMinutes(10));

        $season = Season::factory()->create();
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

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
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $this->assertFalse(Cache::has('stats:season-series-chart'));
    }

    public function test_frame_changes_clear_chart_cache(): void
    {
        Cache::put('stats:season-series-chart', ['cached' => true], now()->addMinutes(10));

        $result = Result::factory()->create();

        Frame::create([
            'result_id' => $result->id,
            'home_player_id' => User::factory()->create()->id,
            'home_score' => 1,
            'away_player_id' => User::factory()->create()->id,
            'away_score' => 0,
        ]);

        $this->assertFalse(Cache::has('stats:season-series-chart'));
    }

    public function test_season_changes_clear_chart_cache(): void
    {
        Cache::put('stats:season-series-chart', ['cached' => true], now()->addMinutes(10));

        $season = Season::factory()->create();

        $season->update(['name' => 'Updated season']);

        $this->assertFalse(Cache::has('stats:season-series-chart'));
    }
}
