<?php

namespace Tests\Feature;

use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use App\Support\ResultShareImageGenerator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResultShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_result_show_displays_archived_season_results_with_trashed_relations(): void
    {
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
            ->assertSee('from-green-900 via-green-800 to-green-700', false)
            ->assertSee('from-red-900 via-red-800 to-red-700', false)
            ->assertSee('dark:bg-zinc-900', false)
            ->assertSee('dark:border-zinc-800/80', false)
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

    public function test_result_show_outputs_result_specific_social_meta_tags(): void
    {
        $result = $this->createShareableResult([
            'home_team_name' => 'Shoulder of Mutton',
            'away_team_name' => 'Junction Inn',
            'home_score' => 8,
            'away_score' => 3,
            'updated_at' => Carbon::parse('2026-03-12 22:15:00'),
        ]);

        $shareImageUrl = app(ResultShareImageGenerator::class)->url($result->fresh());

        $this->get(route('result.show', $result))
            ->assertOk()
            ->assertSee('<meta property="og:title" content="Shoulder of Mutton 8-3 Junction Inn" />', false)
            ->assertSee('<meta property="og:description" content="Premier Division • International Rules • 12 Mar 2026 • Dog & Duck" />', false)
            ->assertSee('<meta property="og:image" content="'.$shareImageUrl.'" />', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image" />', false)
            ->assertSee('<meta name="twitter:image" content="'.$shareImageUrl.'" />', false)
            ->assertSee('data-result-share-button', false);
    }

    public function test_result_share_image_endpoint_returns_png_and_caches_the_current_version(): void
    {
        $generator = app(ResultShareImageGenerator::class);

        if (! $generator->isAvailable()) {
            $this->markTestSkipped('A headless browser is required for share image rendering.');
        }

        Storage::fake('local');

        $result = $this->createShareableResult([
            'updated_at' => Carbon::parse('2026-03-12 22:15:00'),
        ]);

        $currentPath = $generator->cachePath($result->fresh());

        $this->assertFalse(Storage::disk('local')->exists($currentPath));

        $this->get(route('result.share-image.versioned', [
            'result' => $result,
            'version' => $generator->version($result->fresh()),
        ]))
            ->assertOk()
            ->assertHeader('content-type', 'image/png')
            ->assertHeader('cache-control', 'public, max-age=31536000, immutable');

        $this->assertTrue(Storage::disk('local')->exists($currentPath));

        $this->get(route('result.share-image', $result))
            ->assertOk()
            ->assertHeader('content-type', 'image/png')
            ->assertHeader('cache-control', 'public, max-age=300');
    }

    public function test_result_share_image_url_and_cache_path_change_when_the_result_is_updated(): void
    {
        $generator = app(ResultShareImageGenerator::class);

        if (! $generator->isAvailable()) {
            $this->markTestSkipped('A headless browser is required for share image rendering.');
        }

        Storage::fake('local');

        $result = $this->createShareableResult([
            'home_score' => 7,
            'away_score' => 4,
            'updated_at' => Carbon::parse('2026-03-12 22:15:00'),
        ]);

        $initialUrl = $generator->url($result->fresh());
        $initialPath = $generator->cachePath($result->fresh());

        $this->get($initialUrl)->assertOk();
        $this->assertTrue(Storage::disk('local')->exists($initialPath));

        $result->forceFill([
            'home_score' => 8,
            'updated_at' => Carbon::parse('2026-03-13 09:30:00'),
        ])->save();

        $updatedResult = $result->fresh();
        $updatedUrl = $generator->url($updatedResult);
        $updatedPath = $generator->cachePath($updatedResult);

        $this->assertNotSame($initialUrl, $updatedUrl);
        $this->assertNotSame($initialPath, $updatedPath);

        $this->get(route('result.show', $updatedResult))
            ->assertOk()
            ->assertSee('<meta property="og:title" content="Shoulder of Mutton 8-4 Junction Inn" />', false)
            ->assertSee('<meta property="og:image" content="'.$updatedUrl.'" />', false);

        $this->get($updatedUrl)->assertOk();

        $this->assertTrue(Storage::disk('local')->exists($updatedPath));
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createShareableResult(array $overrides = []): Result
    {
        $result = null;

        Model::withoutEvents(function () use (&$result, $overrides): void {
            $season = Season::factory()->create(['is_open' => true]);
            $ruleset = Ruleset::factory()->create(['name' => 'International Rules']);
            $section = Section::factory()->create([
                'season_id' => $season->id,
                'ruleset_id' => $ruleset->id,
                'name' => 'Premier Division',
                'slug' => 'premier-division',
            ]);

            Team::factory()->create();

            $homeTeam = Team::factory()->create(['name' => 'Shoulder of Mutton']);
            $awayTeam = Team::factory()->create(['name' => 'Junction Inn']);

            $section->teams()->attach($homeTeam->id, ['sort' => 1]);
            $section->teams()->attach($awayTeam->id, ['sort' => 2]);

            $homePlayer = User::factory()->create(['team_id' => $homeTeam->id]);
            $awayPlayer = User::factory()->create(['team_id' => $awayTeam->id]);
            $submitter = User::factory()->create(['team_id' => $homeTeam->id]);
            $venue = Venue::factory()->create(['name' => 'Dog & Duck']);

            $fixture = $section->fixtures()->create([
                'week' => 1,
                'fixture_date' => Carbon::parse('2026-03-12'),
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'season_id' => $season->id,
                'venue_id' => $venue->id,
                'ruleset_id' => $ruleset->id,
            ]);

            $result = Result::factory()->create(array_merge([
                'fixture_id' => $fixture->id,
                'home_team_id' => $homeTeam->id,
                'home_team_name' => $homeTeam->name,
                'home_score' => 7,
                'away_team_id' => $awayTeam->id,
                'away_team_name' => $awayTeam->name,
                'away_score' => 4,
                'section_id' => $section->id,
                'ruleset_id' => $ruleset->id,
                'submitted_by' => $submitter->id,
                'is_confirmed' => true,
                'updated_at' => Carbon::parse('2026-03-12 21:00:00'),
            ], $overrides));

            $result->frames()->create([
                'home_player_id' => $homePlayer->id,
                'home_score' => 1,
                'away_player_id' => $awayPlayer->id,
                'away_score' => 0,
            ]);
        });

        return $result;
    }
}
