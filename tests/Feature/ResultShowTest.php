<?php

namespace Tests\Feature;

use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\ResponseCache\Facades\ResponseCache;
use Tests\TestCase;

class ResultShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_result_show_displays_archived_season_results_with_trashed_relations(): void
    {
        Cache::flush();
        ResponseCache::clear();

        $result = null;
        $season = null;
        $ruleset = null;
        $section = null;
        $homeTeam = null;
        $awayTeam = null;

        Model::withoutEvents(function () use (&$result, &$season, &$ruleset, &$section, &$homeTeam, &$awayTeam): void {
            $season = Season::factory()->create(['is_open' => false]);
            $ruleset = Ruleset::factory()->create();
            $section = Section::factory()->create([
                'season_id' => $season->id,
                'ruleset_id' => $ruleset->id,
                'slug' => 'august-2023-international-premier',
                'name' => 'International Premier',
            ]);

            $venue = Venue::factory()->create([
                'name' => 'Archived Venue',
            ]);

            $homeTeam = Team::factory()->create();
            $awayTeam = Team::factory()->create();

            $section->teams()->attach($homeTeam->id, ['sort' => 1]);
            $section->teams()->attach($awayTeam->id, ['sort' => 2]);

            $homePlayer = User::factory()->create(['team_id' => $homeTeam->id]);
            $awayPlayer = User::factory()->create(['team_id' => $awayTeam->id]);
            $submitter = User::factory()->create(['team_id' => $homeTeam->id]);

            $fixture = $section->fixtures()->create([
                'week' => 1,
                'fixture_date' => now()->subYear()->toDateString(),
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'season_id' => $season->id,
                'venue_id' => $venue->id,
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

            $section->delete();
            $venue->delete();
        });

        $this->get(route('result.show', $result))
            ->assertOk()
            ->assertSeeText('International Premier')
            ->assertSeeText('Archived Venue')
            ->assertSee('href="'.route('team.show', $homeTeam).'"', false)
            ->assertSee('href="'.route('team.show', $awayTeam).'"', false)
            ->assertSee('href="'.route('history.section.show', [
                'season' => $season,
                'ruleset' => $ruleset,
                'section' => $section,
                'tab' => 'fixtures-results',
            ]).'"', false);
    }

    public function test_result_show_eager_loads_relations_used_by_the_view(): void
    {
        Cache::flush();
        ResponseCache::clear();

        $result = null;
        $homeTeam = null;
        $awayTeam = null;

        Model::withoutEvents(function () use (&$result, &$homeTeam, &$awayTeam): void {
            $season = Season::factory()->create(['is_open' => true]);
            $ruleset = Ruleset::factory()->create();
            $section = Section::factory()->create([
                'season_id' => $season->id,
                'ruleset_id' => $ruleset->id,
                'slug' => 'test-section',
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
            ->assertSee('data-result-card-shell', false)
            ->assertSee('data-result-card-frames', false)
            ->assertSee('data-result-score-pill', false)
            ->assertSee('data-result-frame-score-pill', false)
            ->assertSee('data-result-share-card-button', false)
            ->assertSee('navigator.share', false)
            ->assertSee('/results/'.$result->id.'/og-image', false)
            ->assertSee('property="og:image:type" content="image/png"', false)
            ->assertSee('property="og:image:width" content="1200"', false)
            ->assertSee('property="og:image:height" content="630"', false)
            ->assertSee('/results/'.$result->id.'"', false)
            ->assertSee('ui-score-pill-segment-win', false)
            ->assertSee('ui-score-pill-segment-loss', false)
            ->assertSee('ui-page-shell', false)
            ->assertSee('data-section-shared-header', false)
            ->assertSee('ui-section', false)
            ->assertSee('ui-shell-grid', false)
            ->assertSee('ui-card', false)
            ->assertSeeText('Result information')
            ->assertSeeText('Result card')
            ->assertSee('href="'.route('team.show', $homeTeam).'"', false)
            ->assertSee('href="'.route('team.show', $awayTeam).'"', false)
            ->assertViewHas('result', function (Result $viewResult): bool {
                return $viewResult->relationLoaded('fixture')
                    && $viewResult->fixture->relationLoaded('season')
                    && $viewResult->fixture->relationLoaded('homeTeam')
                    && $viewResult->fixture->relationLoaded('awayTeam')
                    && $viewResult->fixture->relationLoaded('section')
                    && $viewResult->fixture->section->relationLoaded('ruleset')
                    && $viewResult->fixture->relationLoaded('venue')
                    && $viewResult->relationLoaded('frames')
                    && $viewResult->frames->every(fn ($frame): bool => $frame->relationLoaded('homePlayer'))
                    && $viewResult->frames->every(fn ($frame): bool => $frame->relationLoaded('awayPlayer'))
                    && $viewResult->relationLoaded('submittedBy');
            });
    }

    public function test_result_og_image_endpoint_displays_share_friendly_result_summary(): void
    {
        Cache::flush();
        ResponseCache::clear();

        $season = Season::factory()->create(['is_open' => true, 'name' => 'Spring 2026']);
        $ruleset = Ruleset::factory()->create(['name' => 'Blackball']);
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Premier Division',
        ]);

        $homeTeam = Team::factory()->create(['name' => 'Break Masters']);
        $awayTeam = Team::factory()->create(['name' => 'Cue Kings']);
        $submitter = User::factory()->create(['team_id' => $homeTeam->id, 'name' => 'Jamie Captain']);
        $venue = Venue::factory()->create(['name' => 'Civic Club']);

        $fixture = $section->fixtures()->create([
            'week' => 3,
            'fixture_date' => now()->setDate(2026, 3, 17)->toDateString(),
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'season_id' => $season->id,
            'venue_id' => $venue->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'home_score' => 10,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => 6,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $submitter->id,
            'is_confirmed' => true,
        ]);

        $response = $this->get(route('result.og-image', $result));

        $response
            ->assertOk()
            ->assertHeader('content-type', 'image/png');

        $this->assertStringStartsWith(
            "\x89PNG\r\n\x1a\n",
            (string) $response->getContent()
        );
    }
}
