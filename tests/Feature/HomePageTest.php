<?php

namespace Tests\Feature;

use App\Models\Fixture;
use App\Models\News;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\ResponseCache\Facades\ResponseCache;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        ResponseCache::clear();
    }

    public function test_home_page_renders_a_single_full_bleed_hero(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('data-home-hero', false);
        $response->assertSee('from-green-950 via-green-800 to-green-600 pt-[72px] text-white lg:pt-[80px]', false);
        $response->assertSee('flex flex-col gap-6 lg:flex-row lg:items-center lg:gap-12', false);
        $response->assertSee('lg:flex-none lg:justify-start', false);
        $response->assertSee('relative w-36 drop-shadow-2xl sm:w-40 lg:w-44', false);
        $response->assertSeeText('Everything for league night, in one place.');
        $response->assertSeeText('Tables, fixtures, results and averages for every section');
        $response->assertSee('src="'.asset('images/logo.png').'"', false);
        $response->assertSee('data-home-live-scores', false);
        $response->assertSeeText('Live scores');
        $response->assertSeeText('No current matches in progress right now.');
        $response->assertSee('data-home-news', false);
        $response->assertSeeText('Latest news');
        $response->assertSee('data-home-news-empty', false);
        $response->assertSeeText('No league news has been published yet.');
        $response->assertSee('data-section-sponsors', false);
        $response->assertSee('data-section-sponsors-grid', false);
        $response->assertSee('mx-auto max-w-7xl px-4 sm:px-6 lg:px-8', false);
        $response->assertSee('lg:grid-cols-[minmax(0,19rem)_minmax(0,1fr)] lg:items-start lg:gap-12', false);
        $response->assertSeeText('Backing the league every week');
        $response->assertDontSee('tracking-[0.28em] text-green-100', false);
        $response->assertDontSee('min-h-[calc(100dvh-72px)]', false);
        $response->assertDontSeeText('Upcoming fixtures');
        $response->assertDontSeeText('My Team');
        $response->assertDontSeeText('Pending actions');
    }

    public function test_signed_in_users_see_the_same_hero_only_home_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertSee('data-home-hero', false);
        $response->assertSeeText('Everything for league night, in one place.');
        $response->assertDontSeeText('My Team');
        $response->assertDontSeeText('Pending actions');
    }

    public function test_home_page_shows_live_scores_for_results_in_progress(): void
    {
        $data = $this->createLiveScoreFixtureData();

        $result = Result::factory()->create([
            'fixture_id' => $data['fixture']->id,
            'home_team_id' => $data['homeTeam']->id,
            'home_team_name' => $data['homeTeam']->name,
            'home_score' => 6,
            'away_team_id' => $data['awayTeam']->id,
            'away_team_name' => $data['awayTeam']->name,
            'away_score' => 4,
            'is_confirmed' => false,
            'section_id' => $data['section']->id,
            'ruleset_id' => $data['ruleset']->id,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('data-home-live-scores-shell', false);
        $response->assertSee('data-home-live-scores-band', false);
        $response->assertSee('data-home-live-scores-list', false);
        $response->assertSee('data-home-live-score-pill', false);
        $response->assertSeeText('Break Masters');
        $response->assertSeeText('Cue Kings');
        $response->assertSee('href="'.route('result.show', $result).'"', false);
        $response->assertDontSeeText('No current matches in progress right now.');
    }

    public function test_home_page_shows_latest_news_in_a_featured_layout(): void
    {
        $author = User::factory()->create(['name' => 'John Bell']);

        News::withoutEvents(function () use ($author): void {
            News::query()->create([
                'title' => 'Captains meeting',
                'content' => "Captains should arrive for 7:15pm.\nImportant league notices will be covered before the break.",
                'author_id' => $author->id,
            ]);

            News::query()->create([
                'title' => 'Fixture dates updated',
                'content' => 'Several fixture dates have changed following venue availability updates.',
                'author_id' => $author->id,
            ]);
        });

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('data-home-news', false);
        $response->assertSee('data-home-news-grid', false);
        $response->assertSee('data-home-news-featured', false);
        $response->assertSee('data-home-news-list', false);
        $response->assertSee('data-home-news-item', false);
        $response->assertSeeText('Fixture dates updated');
        $response->assertSeeText('Captains meeting');
        $response->assertSeeText('15 March 2026');
        $response->assertDontSee('data-home-news-empty', false);
    }

    public function test_home_page_response_cache_is_cleared_when_live_scores_change(): void
    {
        $data = $this->createLiveScoreFixtureData();

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('No current matches in progress right now.');

        Result::factory()->create([
            'fixture_id' => $data['fixture']->id,
            'home_team_id' => $data['homeTeam']->id,
            'home_team_name' => $data['homeTeam']->name,
            'home_score' => 6,
            'away_team_id' => $data['awayTeam']->id,
            'away_team_name' => $data['awayTeam']->name,
            'away_score' => 4,
            'is_confirmed' => false,
            'section_id' => $data['section']->id,
            'ruleset_id' => $data['ruleset']->id,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-home-live-scores-list', false)
            ->assertSeeText('Break Masters')
            ->assertSeeText('Cue Kings')
            ->assertDontSeeText('No current matches in progress right now.');
    }

    public function test_home_page_response_cache_is_cleared_when_news_changes(): void
    {
        $user = User::factory()->create();

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('No league news has been published yet.');

        $this->actingAs($user);

        News::query()->create([
            'title' => 'League handbook update',
            'content' => 'The latest handbook revision is now available for all captains and players.',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-home-news-featured', false)
            ->assertSeeText('League handbook update')
            ->assertDontSeeText('No league news has been published yet.');
    }

    public function test_home_page_ignores_stale_navigation_ruleset_cache_entries(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create([
            'name' => 'Blackball Rules',
            'slug' => 'blackball-rules',
        ]);
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Blackball Premier',
        ]);

        $staleRuleset = Ruleset::query()
            ->with([
                'openSections' => fn ($query) => $query
                    ->select(['id', 'name', 'ruleset_id', 'season_id'])
                    ->with('season')
                    ->orderBy('name'),
            ])
            ->findOrFail($ruleset->id);

        Cache::put('nav:rulesets', collect([$staleRuleset]), now()->addMinutes(10));

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee(route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section]), false);
    }

    public function test_home_page_ignores_invalid_current_navigation_section_cache_entries(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create([
            'name' => 'Blackball Rules',
            'slug' => 'blackball-rules',
        ]);

        Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Blackball Premier',
        ]);

        $invalidCachedRuleset = Ruleset::query()
            ->with([
                'openSections' => fn ($query) => $query
                    ->select(['id', 'name', 'ruleset_id', 'season_id'])
                    ->with('season')
                    ->orderBy('name'),
            ])
            ->findOrFail($ruleset->id);

        Cache::put('nav:rulesets:v4', collect([$invalidCachedRuleset]), now()->addMinutes(10));

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSeeText('Blackball Premier');
    }

    /**
     * @return array{
     *     season: Season,
     *     ruleset: Ruleset,
     *     section: Section,
     *     homeTeam: Team,
     *     awayTeam: Team,
     *     fixture: Fixture
     * }
     */
    private function createLiveScoreFixtureData(): array
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create([
            'name' => 'International Rules',
        ]);
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Premier Division',
        ]);

        Team::factory()->create();
        $homeTeam = Team::factory()->create(['name' => 'Break Masters']);
        $awayTeam = Team::factory()->create(['name' => 'Cue Kings']);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'fixture_date' => now(),
        ]);

        return compact('season', 'ruleset', 'section', 'homeTeam', 'awayTeam', 'fixture');
    }
}
