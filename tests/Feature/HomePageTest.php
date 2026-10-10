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
use Illuminate\Support\Facades\Storage;
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

    public function test_home_page_renders_a_shadcn_style_hero_with_the_centered_league_logo(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('data-home-page', false);
        $response->assertSee('ui-page-shell', false);
        $response->assertSee('data-home-hero', false);
        $response->assertDontSee('/livewire/livewire.min.js', false);
        $response->assertDontSee('window.livewireScriptConfig', false);
        $response->assertSee('mx-auto flex max-w-6xl flex-col items-center gap-1.5 px-6 py-4 text-center sm:gap-2 md:py-6 lg:py-8 xl:gap-3', false);
        $response->assertDontSeeText('Huddersfield & District Pool League');
        $response->assertSee('text-3xl leading-[1.1] font-semibold tracking-tight text-balance text-gray-900', false);
        $response->assertSee('max-w-2xl text-base leading-6 text-gray-900 sm:text-lg sm:leading-7 dark:text-gray-100', false);
        $response->assertSee('flex w-full items-center justify-center gap-2 pt-1', false);
        $response->assertSee('data-home-hero-actions', false);
        $response->assertSee('data-home-hero-account-action', false);
        $response->assertSee('href="'.route('login').'"', false);
        $response->assertSeeText('Log in to view your account');
        $response->assertSee('data-home-hero-logo', false);
        $response->assertSee('data-home-hero-title', false);
        $response->assertSee('data-home-hero-description', false);
        $response->assertSee('data-home-hero-actions', false);
        $response->assertSee('class="h-24 w-24 object-contain sm:h-28 sm:w-28 lg:h-32 lg:w-32"', false);
        $response->assertSee('alt="Huddersfield Pool League logo"', false);
        $response->assertSee(asset('images/logo-160.webp').'?v=', false);
        $response->assertSee(asset('images/logo-320.webp').'?v=', false);
        $response->assertSee('id="live-scores"', false);
        $response->assertSee('group/button inline-flex h-[35px] shrink-0 items-center justify-center gap-1.5 rounded-full', false);
        $response->assertSee('bg-black px-4 text-sm leading-5 font-medium whitespace-nowrap text-white', false);
        $response->assertDontSee('ui-card-branded', false);
        $response->assertDontSee('ui-section ui-card-body', false);
        $response->assertSee('@keydown.arrow-down.prevent="moveActiveResult(1)"', false);
        $response->assertSee('@keydown.arrow-up.prevent="moveActiveResult(-1)"', false);
        $response->assertSee('@keydown.enter.prevent="openActiveResult()"', false);
        $response->assertSee(':aria-activedescendant="activeResultId()"', false);
        $response->assertSeeText('Everything for league night, in one place.');
        $response->assertSeeText('Tables, fixtures, results and averages for every section');
        $response->assertDontSee('data-home-hero-account-link', false);
        $response->assertSee('data-home-live-scores', false);
        $response->assertSee('x-data="window.homeLiveScoresMotion()"', false);
        $response->assertSeeText('Live scores');
        $response->assertSee('flex size-6 shrink-0 items-center justify-center', false);
        $response->assertSee('icon icon-tabler icons-tabler-outline icon-tabler-bolt size-5 text-neutral-700 dark:text-neutral-200', false);
        $response->assertSee('<path d="M13 3l0 7l6 0l-8 11l0 -7l-6 0l8 -11" />', false);
        $response->assertSee('ui-section-intro-copy grid auto-rows-min items-start gap-1.5', false);
        $response->assertSee('font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50', false);
        $response->assertSee('m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400', false);
        $response->assertSee('mx-auto max-w-4xl px-4 sm:px-6 lg:px-6', false);
        $response->assertSee('ui-shell-grid', false);
        $response->assertSee('ui-card', false);
        $response->assertSee('ui-live-scores-card', false);
        $response->assertSeeText('No live scores to show right now.');
        $response->assertSee('data-home-news', false);
        $response->assertSeeText('Latest news');
        $response->assertSee('icon icon-tabler icons-tabler-outline icon-tabler-news size-5 text-neutral-700 dark:text-neutral-200', false);
        $response->assertSee('<path d="M8 8l4 0" />', false);
        $response->assertSee('ui-section-intro-copy grid auto-rows-min items-start gap-1.5', false);
        $response->assertSee('data-home-news-empty', false);
        $response->assertSeeText('No league news has been published yet.');
        $response->assertSee('data-section-sponsors', false);
        $response->assertSee('data-section-sponsors-carousel', false);
        $response->assertSee('x-data="window.sponsorCarousel(6, 3)"', false);
        $response->assertSee('x-on:touchstart.passive="handleTouchStart($event)"', false);
        $response->assertSee('x-on:touchend.passive="handleTouchEnd($event)"', false);
        $response->assertSee('x-on:click.capture="handleSwipeClick($event)"', false);
        $response->assertSee('aria-roledescription="carousel"', false);
        $response->assertSee('basis-1/2 pl-3 lg:basis-1/3', false);
        $response->assertSee('ui-sponsor-carousel-button', false);
        $response->assertSee('icon icon-tabler icons-tabler-outline icon-tabler-rocket size-5 text-neutral-700 dark:text-neutral-200', false);
        $response->assertSee('<path d="M4 13a8 8 0 0 1 7 7', false);
        $response->assertSee('font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50', false);
        $response->assertSee('m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400', false);
        $response->assertSee('mx-auto max-w-4xl px-4 sm:px-6 lg:px-6', false);
        $response->assertSee('ui-shell-grid', false);
        $response->assertSee('ui-card', false);
        $response->assertSee('ui-sponsors-card', false);
        $response->assertSee('ui-sponsor-item', false);
        $response->assertSee('ui-sponsor-card-media', false);
        $response->assertSee('ui-sponsor-content', false);
        $response->assertSee('ui-sponsor-logo', false);
        $response->assertSeeText('Backing the league every week');
        $response->assertSeeText('Meet the local businesses helping keep league nights running.');
        $response->assertSee(asset('images/sponsors/nrkfabrication-logo-160.webp').'?v=', false);
        $response->assertSee(asset('images/sponsors/ukplasticsandglazing-logo-160.webp').'?v=', false);
        $response->assertSee(asset('images/sponsors/thepooltableguru-160.webp').'?v=', false);
        $response->assertSee('loading="lazy"', false);
        $response->assertSee('decoding="async"', false);
        $response->assertSee('<footer class="bg-neutral-100 dark:bg-neutral-950">', false);
        $response->assertSee('href="mailto:john@thebiggerboat.co.uk"', false);
        $response->assertSee('href="https://www.thebiggerboat.co.uk/"', false);
        $response->assertSeeText('Built by');
        $response->assertSeeText('John Bell');
        $response->assertSeeText('The Bigger Boat');
        $response->assertDontSeeText('Website built by John Bell.');
        $response->assertDontSeeText('Privacy');
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
        $response->assertSee('data-home-hero-account-action', false);
        $response->assertSee('href="'.route('account.show').'"', false);
        $response->assertSeeText('View your account');
        $response->assertDontSee('data-home-hero-account-link', false);
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
        $response->assertSee('data-home-hero-registration', false);
        $response->assertDontSee('data-home-hero-live-scores', false);
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
        $response->assertSee('data-home-hero-account-action', false);
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
        $response->assertSee('data-home-hero-account-action', false);
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
        $response->assertSee('ui-live-scores-card', false);
        $response->assertSee('ui-live-scores-item-group max-h-80 overflow-y-auto overscroll-contain', false);
        $response->assertSee('ui-card-row-link', false);
        $response->assertSee('ui-live-score-item', false);
        $response->assertSee('ui-live-score-item-content', false);
        $response->assertSee('ui-live-score-team-matchup', false);
        $response->assertSee('ui-live-score-item-actions', false);
        $response->assertSee('ui-live-score-badge-stack', false);
        $response->assertSee('ui-live-score-badge', false);
        $response->assertSee('ui-live-score-badge-win', false);
        $response->assertSee('data-slot="item"', false);
        $response->assertSee('data-slot="item-content"', false);
        $response->assertSee('data-slot="item-actions"', false);
        $response->assertSee('data-slot="badge"', false);
        $response->assertSee('data-variant="muted"', false);
        $response->assertSee('rounded-full', false);
        $response->assertSee('role="group"', false);
        $response->assertDontSee('ui-live-score-button-group', false);
        $response->assertSee('data-home-live-score-row', false);
        $response->assertSee('data-home-live-score-key="'.$result->id.'"', false);
        $response->assertSee('data-home-live-score-pill', false);
        $response->assertSee('sm:hidden', false);
        $response->assertSee('sm:block', false);
        $response->assertSee('ui-live-score-section-name line-clamp-2 text-left', false);
        $response->assertSeeText('Break Masters');
        $response->assertSeeText('Cue Kings');
        $response->assertSeeText('Premier Division');
        $response->assertDontSeeText($data['fixture']->fixture_date->format('j M Y'));
        $response->assertSee('href="'.route('result.show', $result).'"', false);
        $response->assertDontSeeText('No live scores to show right now.');
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

    public function test_home_page_links_site_admin_not_on_fixture_team_to_resume_in_progress_match(): void
    {
        $data = $this->createLiveScoreFixtureData();

        $admin = User::factory()->create([
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
        $response->assertSee('ui-live-scores-item-group max-h-80 overflow-y-auto overscroll-contain', false);
        $this->assertSame(7, substr_count($response->getContent(), 'data-home-live-score-row'));
        $response->assertSeeText('Home Team 1');
        $response->assertSeeText('Away Team 7');
    }

    public function test_home_page_shows_latest_news_in_card_rows(): void
    {
        Storage::fake('public');

        $author = User::factory()->create(['name' => 'John Bell']);
        $expectedDate = now()->format('j F Y');

        News::withoutEvents(function () use ($author): void {
            News::query()->create([
                'title' => 'Captains meeting',
                'slug' => 'captains-meeting',
                'content' => "Captains should arrive for 7:15pm.\nImportant league notices will be covered before the break.",
                'published_at' => now()->subDay(),
                'author_id' => $author->id,
            ]);

            $featuredArticle = News::query()->create([
                'title' => 'Fixture dates updated',
                'slug' => 'fixture-dates-updated',
                'content' => 'Several fixture dates have changed following venue availability updates.',
                'published_at' => now(),
                'author_id' => $author->id,
            ]);

            $featuredArticle->addMediaFromString('featured-image')
                ->usingFileName('fixture-dates.jpg')
                ->usingName('fixture-dates')
                ->toMediaCollection('featured-images', 'public');

            News::query()->create([
                'title' => 'Draft article',
                'slug' => 'draft-article',
                'content' => 'This draft should stay hidden from the homepage.',
                'published_at' => null,
                'author_id' => $author->id,
            ]);
        });

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('data-home-news', false);
        $response->assertSee('data-home-news-rows', false);
        $response->assertSee('data-home-news-list', false);
        $response->assertSee('data-home-news-item', false);
        $response->assertSee('data-home-news-card', false);
        $response->assertSeeText('Fixture dates updated');
        $response->assertSeeText('Captains meeting');
        $response->assertDontSeeText('Draft article');
        $response->assertSeeText($expectedDate);
        $response->assertDontSee('href="'.route('news.show', News::query()->published()->latest('published_at')->firstOrFail()).'"', false);
        $response->assertDontSee('data-home-news-featured-image', false);
        $response->assertDontSee('See more');
        $response->assertDontSee('href="'.route('news.index').'"', false);
        $response->assertDontSee('data-home-news-empty', false);
    }

    public function test_home_page_response_cache_is_cleared_when_live_scores_change(): void
    {
        $data = $this->createLiveScoreFixtureData();

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('No live scores to show right now.');

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
            ->assertDontSeeText('No live scores to show right now.');
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
            'published_at' => now(),
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-home-news-rows', false)
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
