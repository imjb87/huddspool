<?php

namespace Tests\Feature;

use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_result_show_eager_loads_relations_used_by_the_view(): void
    {
        $result = null;

        Model::withoutEvents(function () use (&$result): void {
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
            $submitter = User::factory()->create(['team_id' => $homeTeam->id]);

            $fixture = $section->fixtures()->create([
                'week' => 1,
                'fixture_date' => now()->subDay()->toDateString(),
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'season_id' => $season->id,
                'venue_id' => $homeTeam->venue_id,
                'ruleset_id' => $ruleset->id,
            ]);

            $result = Result::factory()->create([
                'fixture_id' => $fixture->id,
                'home_team_id' => $homeTeam->id,
                'home_team_name' => $homeTeam->name,
                'away_team_id' => $awayTeam->id,
                'away_team_name' => $awayTeam->name,
                'section_id' => $section->id,
                'ruleset_id' => $ruleset->id,
                'submitted_by' => $submitter->id,
                'is_confirmed' => true,
            ]);

            $result->frames()->create([
                'home_player_id' => $homePlayer->id,
                'home_score' => 1,
                'away_player_id' => $awayPlayer->id,
                'away_score' => 0,
            ]);
        });

        $this->get(route('result.show', $result))
            ->assertOk()
            ->assertSee('data-result-page', false)
            ->assertSee('data-result-info-section', false)
            ->assertSee('data-result-card-section', false)
            ->assertSee('dark:bg-zinc-900', false)
            ->assertSee('dark:border-zinc-800/80', false)
            ->assertSeeText('Result information')
            ->assertSeeText('Result card')
            ->assertViewHas('result', function (Result $viewResult): bool {
                return $viewResult->relationLoaded('fixture')
                    && $viewResult->fixture->relationLoaded('section')
                    && $viewResult->fixture->section->relationLoaded('ruleset')
                    && $viewResult->fixture->relationLoaded('venue')
                    && $viewResult->relationLoaded('frames')
                    && $viewResult->frames->every(fn ($frame): bool => $frame->relationLoaded('homePlayer'))
                    && $viewResult->frames->every(fn ($frame): bool => $frame->relationLoaded('awayPlayer'))
                    && $viewResult->relationLoaded('submittedBy');
            });
    }
}
