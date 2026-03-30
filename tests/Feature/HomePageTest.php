<?php

namespace Tests\Feature;

use App\Enums\UserRole;
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
        $response->assertSee('data-home-page', false);
        $response->assertSee('ui-page-shell', false);
        $response->assertSee('data-home-hero', false);
        $response->assertDontSee('/livewire/livewire.min.js', false);
        $response->assertDontSee('window.livewireScriptConfig', false);
        $response->assertSee('mx-auto max-w-4xl px-4 sm:px-6 lg:px-6', false);
        $response->assertSee('ui-card-branded', false);
        $response->assertSee('ui-section ui-card-body', false);
        $response->assertSee('ui-shell-grid items-center', false);
        $response->assertSee('flex justify-start lg:justify-center', false);
        $response->assertSee('text-left lg:col-span-2', false);
        $response->assertSee('relative w-32 drop-shadow-2xl sm:w-36 lg:w-40', false);
        $response->assertSee(asset('images/logo-160.webp').'?v=', false);
        $response->assertSee(asset('images/logo-320.webp').'?v=', false);
        $response->assertSee('rel="preload"', false);
        $response->assertSee('as="image"', false);
        $response->assertSee('imagesizes="(min-width: 1024px) 160px, (min-width: 640px) 144px, 128px"', false);
        $response->assertSee('sizes="(min-width: 1024px) 160px, (min-width: 640px) 144px, 128px"', false);
        $response->assertSee('width="160"', false);
        $response->assertSee('height="160"', false);
        $response->assertSee('loading="eager"', false);
        $response->assertSee('fetchpriority="high"', false);
        $response->assertSeeText('Everything for league night, in one place.');
        $response->assertSeeText('Tables, fixtures, results and averages for every section');
        $response->assertSee('data-home-hero-account-link', false);
        $response->assertSee('href="'.route('login').'"', false);
        $response->assertSeeText('Log in');
        $response->assertSee('src="'.asset('images/logo-320.png').'?v=', false);
        $response->assertSee('data-home-live-scores', false);
        $response->assertSeeText('Live scores');
        $response->assertSee('mx-auto max-w-4xl px-4 sm:px-6 lg:px-6', false);
        $response->assertSee('ui-shell-grid', false);
        $response->assertSee('ui-card', false);
        $response->assertSeeText('No current matches in progress right now.');
        $response->assertSee('data-home-news', false);
        $response->assertSeeText('Latest news');
        $response->assertSee('data-home-news-empty', false);
        $response->assertSeeText('No league news has been published yet.');
        $response->assertSee('data-section-sponsors', false);
        $response->assertSee('data-section-sponsors-grid', false);
        $response->assertSee('mx-auto max-w-4xl px-4 sm:px-6 lg:px-6', false);
        $response->assertSee('ui-shell-grid', false);
        $response->assertSee('ui-card', false);
        $response->assertSeeText('Backing the league every week');
        $response->assertSeeText('Local businesses supporting the league. Visit the sponsors behind the tables, fixtures and nights out.');
        $response->assertSee(asset('images/sponsors/nrkfabrication-logo-160.webp').'?v=', false);
        $response->assertSee(asset('images/sponsors/ukplasticsandglazing-logo-160.webp').'?v=', false);
        $response->assertSee(asset('images/sponsors/thepooltableguru-160.webp').'?v=', false);
        $response->assertSee('loading="lazy"', false);
        $response->assertSee('decoding="async"', false);
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
        $response->assertSee('data-home-page', false);
        $response->assertSee('data-home-hero', false);
        $response->assertSeeText('Everything for league night, in one place.');
        $response->assertSee('data-home-hero-account-link', false);
        $response->assertSee('href="'.route('account.show').'"', false);
        $response->assertSeeText('account');
        $response->assertDontSee('href="'.route('login').'"', false);
        $response->assertDontSeeText('My Team');
        $response->assertDontSeeText('Pending actions');
    }

    public function test_home_page_shows_active_season_entry_countdown_in_the_hero(): void
    {
        $season = Season::factory()->create([
            'name' => 'Summer 2026',
            'signup_opens_at' => now()->subDay(),
            'signup_closes_at' => now()->addDays(5),
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('data-home-hero-entry-countdown', false);
        $response->assertSeeText('Closes in');
        $response->assertSeeText('Registration for the next season is now open');
        $response->assertSeeText('League registration is now open for Summer 2026 until');
        $response->assertSeeText('Registration covers your teams, knockout entries and the key details needed for the upcoming season.');
        $response->assertSeeText('Register now');
        $response->assertSee(route('season.entry.show', ['season' => $season]), false);
        $response->assertDontSee('data-home-hero-account-link', false);
    }

    public function test_home_page_does_not_show_registration_hero_for_a_season_that_has_not_opened_yet(): void
    {
        Season::factory()->create([
            'name' => 'Autumn 2026',
            'signup_opens_at' => now()->addDays(3),
            'signup_closes_at' => now()->addDays(10),
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('data-home-hero-entry-countdown', false);
        $response->assertDontSeeText('Registration for the next season is now open');
        $response->assertDontSeeText('Autumn 2026');
        $response->assertSee('data-home-hero-account-link', false);
    }

    public function test_home_page_does_not_show_registration_hero_for_a_season_without_signup_dates(): void
    {
        Season::factory()->create([
            'name' => 'Winter 2026',
            'signup_opens_at' => null,
            'signup_closes_at' => null,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('data-home-hero-entry-countdown', false);
        $response->assertDontSeeText('Registration for the next season is now open');
        $response->assertDontSeeText('Winter 2026');
        $response->assertSee('data-home-hero-account-link', false);
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
        $response->assertSee('data-home-live-scores-list', false);
        $response->assertSee('ui-card', false);
        $response->assertSee('ui-card-rows max-h-80 overflow-y-auto overscroll-contain', false);
        $response->assertSee('ui-card-row-link', false);
        $response->assertSee('ui-card-row items-start', false);
        $response->assertSee('data-home-live-score-row', false);
        $response->assertSee('data-home-live-score-pill', false);
        $response->assertSeeText('Break Masters');
        $response->assertSeeText('Cue Kings');
        $response->assertSeeText('Premier Division');
        $response->assertSeeText('International Rules');
        $response->assertSeeText($data['fixture']->fixture_date->format('j M Y'));
        $response->assertSee('href="'.route('result.show', $result).'"', false);
        $response->assertDontSeeText('No current matches in progress right now.');
    }

    public function test_home_page_links_team_admin_to_resume_in_progress_match(): void
    {
        $data = $this->createLiveScoreFixtureData();

        $teamAdmin = User::factory()->create([
            'team_id' => $data['homeTeam']->id,
            'role' => UserRole::TeamAdmin->value,
        ]);

        $result = Result::factory()->create([
            'fixture_id' => $data['fixture']->id,
            'home_team_id' => $data['homeTeam']->id,
            'home_team_name' => $data['homeTeam']->name,
            'away_team_id' => $data['awayTeam']->id,
            'away_team_name' => $data['awayTeam']->name,
            'home_score' => 3,
            'away_score' => 2,
            'is_confirmed' => false,
            'section_id' => $data['section']->id,
            'ruleset_id' => $data['ruleset']->id,
        ]);

        $this->actingAs($teamAdmin)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('href="'.route('result.create', $data['fixture']).'"', false)
            ->assertDontSee('href="'.route('result.show', $result).'"', false);
    }

    public function test_home_page_links_team_captain_to_result_show_when_they_lack_submit_permission(): void
    {
        $data = $this->createLiveScoreFixtureData();

        $captain = User::factory()->create([
            'team_id' => $data['awayTeam']->id,
            'role' => UserRole::Player->value,
        ]);
        $data['awayTeam']->update(['captain_id' => $captain->id]);

        $result = Result::factory()->create([
            'fixture_id' => $data['fixture']->id,
            'home_team_id' => $data['homeTeam']->id,
            'home_team_name' => $data['homeTeam']->name,
            'away_team_id' => $data['awayTeam']->id,
            'away_team_name' => $data['awayTeam']->name,
            'home_score' => 4,
            'away_score' => 4,
            'is_confirmed' => false,
            'section_id' => $data['section']->id,
            'ruleset_id' => $data['ruleset']->id,
        ]);

        $this->actingAs($captain)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('href="'.route('result.show', $result).'"', false)
            ->assertDontSee('href="'.route('result.create', $data['fixture']).'"', false);
    }

    public function test_home_page_links_site_admin_on_fixture_team_to_resume_in_progress_match(): void
    {
        $data = $this->createLiveScoreFixtureData();

        $admin = User::factory()->create([
            'team_id' => $data['homeTeam']->id,
            'is_admin' => true,
        ]);
        $admin->assignRole('admin');

        $result = Result::factory()->create([
            'fixture_id' => $data['fixture']->id,
            'home_team_id' => $data['homeTeam']->id,
            'home_team_name' => $data['homeTeam']->name,
            'away_team_id' => $data['awayTeam']->id,
            'away_team_name' => $data['awayTeam']->name,
            'home_score' => 3,
            'away_score' => 2,
            'is_confirmed' => false,
            'section_id' => $data['section']->id,
            'ruleset_id' => $data['ruleset']->id,
        ]);

        $this->actingAs($admin)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('href="'.route('result.create', $data['fixture']).'"', false)
            ->assertDontSee('href="'.route('result.show', $result).'"', false);
    }

    public function test_home_page_live_scores_list_keeps_all_in_progress_results_in_a_scrollable_five_row_container(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create([
            'name' => 'EPA Rules',
        ]);
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'EPA Section One',
        ]);

        Team::factory()->create();

        for ($index = 1; $index <= 7; $index++) {
            $homeTeam = Team::factory()->create(['name' => "Home Team {$index}"]);
            $awayTeam = Team::factory()->create(['name' => "Away Team {$index}"]);

            $fixture = Fixture::factory()->create([
                'season_id' => $season->id,
                'section_id' => $section->id,
                'ruleset_id' => $ruleset->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'fixture_date' => now()->subMinutes($index),
            ]);

            Result::factory()->create([
                'fixture_id' => $fixture->id,
                'home_team_id' => $homeTeam->id,
                'home_team_name' => $homeTeam->name,
                'home_score' => 6,
                'away_team_id' => $awayTeam->id,
                'away_team_name' => $awayTeam->name,
                'away_score' => 4,
                'is_confirmed' => false,
                'section_id' => $section->id,
                'ruleset_id' => $ruleset->id,
            ]);
        }

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('ui-card-rows max-h-80 overflow-y-auto overscroll-contain', false);
        $this->assertSame(7, substr_count($response->getContent(), 'data-home-live-score-row'));
        $response->assertSeeText('Home Team 1');
        $response->assertSeeText('Away Team 7');
    }

    public function test_home_page_shows_latest_news_in_a_featured_layout(): void
    {
        $author = User::factory()->create(['name' => 'John Bell']);
        $expectedDate = now()->format('j F Y');

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
        $response->assertSeeText($expectedDate);
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
            'fixture_date' => now()->setDate(2026, 3, 17),
        ]);

        return compact('season', 'ruleset', 'section', 'homeTeam', 'awayTeam', 'fixture');
    }
}
