<?php

namespace Tests\Feature;

use App\KnockoutType;
use App\Livewire\History\SectionPage as HistorySectionPage;
use App\Models\Fixture;
use App\Models\Frame;
use App\Models\Knockout;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

class HistoryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_top_level_history_page_is_removed(): void
    {
        $this->get('/history')->assertNotFound();
    }

    public function test_history_index_route_is_removed(): void
    {
        $this->assertNull(Route::getRoutes()->getByName('history.index'));
    }

    public function test_history_season_route_is_not_defined(): void
    {
        $season = Season::factory()->create([
            'name' => '2020/21 Season',
            'is_open' => false,
        ]);

        $this->get("/history/{$season->slug}")
            ->assertNotFound();
    }

    public function test_history_ruleset_route_is_not_defined(): void
    {
        $ruleset = Ruleset::factory()->create(['name' => 'World Rules']);
        $season = Season::factory()->create([
            'name' => '2021/22 Season',
            'slug' => '2021-22-season',
            'is_open' => false,
        ]);

        Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $season->id,
            'name' => 'Division A',
        ]);

        Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $season->id,
            'name' => 'Division B',
        ]);

        $this->get("/history/{$season->slug}/{$ruleset->slug}")
            ->assertNotFound();
    }

    public function test_history_routes_use_prefixed_canonical_pages(): void
    {
        $ruleset = Ruleset::factory()->create([
            'slug' => 'world-rules',
        ]);
        $season = Season::factory()->create([
            'name' => '2021/22 Season',
            'slug' => '2021-22-season',
            'is_open' => false,
        ]);
        $section = Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $season->id,
            'name' => 'Division A',
        ]);

        $this->assertSame(
            '/history/202122-season/world-rules/division-a',
            route('history.section.show', [$season, $ruleset, $section], false)
        );
    }

    public function test_history_knockout_routes_use_prefixed_canonical_pages(): void
    {
        $season = Season::factory()->create([
            'name' => '2021/22 Season',
            'slug' => '2021-22-season',
            'is_open' => false,
        ]);
        $knockout = Knockout::query()->create([
            'season_id' => $season->id,
            'name' => 'Singles Cup',
            'slug' => 'singles-cup',
            'type' => KnockoutType::Singles->value,
        ]);

        $this->assertSame(
            '/history/202122-season/knockouts/singles-cup',
            route('history.knockout.show', ['season' => $season, 'knockout' => $knockout], false)
        );
    }

    public function test_history_section_route_displays_dark_mode_ready_historical_overview(): void
    {
        $ruleset = Ruleset::factory()->create(['name' => 'World Rules']);
        $season = Season::factory()->create([
            'name' => '2021/22 Season',
            'is_open' => false,
        ]);
        $section = Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $season->id,
            'name' => 'Division A',
        ]);

        $response = $this->get(route('history.section.show', [$season, $ruleset, $section]));

        $response->assertOk();
    }

    public function test_history_section_page_replicates_section_tabs_and_displays_trashed_records(): void
    {
        $ruleset = Ruleset::factory()->create(['name' => 'World Rules']);
        $season = Season::factory()->create([
            'name' => '2021/22 Season',
            'is_open' => false,
            'dates' => [now()->subWeeks(2)->toDateString(), now()->subWeek()->toDateString()],
        ]);
        $section = Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $season->id,
            'name' => 'Division A',
        ]);
        $otherSection = Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $season->id,
            'name' => 'Division B',
        ]);

        $homeTeam = Team::factory()->create(['name' => 'Reds']);
        $awayTeam = Team::factory()->create(['name' => 'Blues']);

        $section->teams()->attach([
            $homeTeam->id => ['sort' => 1],
            $awayTeam->id => ['sort' => 2],
        ]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'week' => 2,
            'fixture_date' => now()->subWeek(),
        ]);

        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'home_score' => 6,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => 4,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => true,
        ]);

        $homePlayer = User::factory()->create(['name' => 'Archived Alice', 'team_id' => $homeTeam->id]);
        $awayPlayer = User::factory()->create(['name' => 'Active Bob', 'team_id' => $awayTeam->id]);

        Frame::create([
            'result_id' => $result->id,
            'home_player_id' => $homePlayer->id,
            'home_score' => 1,
            'away_player_id' => $awayPlayer->id,
            'away_score' => 0,
        ]);

        $homeTeam->update(['name' => 'Modern Reds']);
        $awayTeam->update(['name' => 'Modern Blues']);

        $homeTeam->delete();
        $homePlayer->delete();

        $response = $this->get(route('history.section.show', [$season, $ruleset, $section]));

        $response->assertOk();
        $response->assertSeeLivewire(HistorySectionPage::class);
        $response->assertSee('data-history-section-page', false);
        $response->assertSee('ui-page-shell', false);
        $response->assertSee('data-section-tabs', false);
        $response->assertSee('data-section-tabs-scroll', false);
        $response->assertSee('tabindex="0"', false);
        $response->assertSee('aria-label="Section tabs"', false);
        $response->assertSee('data-ruleset-active-panel="tables"', false);
        $response->assertSee('bg-card text-card-foreground ring-1 ring-foreground/10', false);
        $response->assertDontSee('sticky top-[72px] z-30 bg-linear-to-br from-green-900 via-green-800 to-green-700 shadow-xl', false);
        $response->assertDontSee('data-section-tab-indicator', false);
        $response->assertSeeText('Division A');
        $response->assertSeeText('2021/22 Season');
        $response->assertSeeText('Reds');
        $response->assertDontSeeText('Modern Reds');
        $response->assertSee('data-section-see-also', false);
        $response->assertSee('ui-card ui-section-see-also-card', false);
        $response->assertSee('ui-section-see-also-list', false);
        $response->assertSee('ui-section-see-also-item', false);
        $response->assertSee('data-size="sm"', false);
        $response->assertSee('ui-section-see-also-item-action', false);
        $response->assertSee('m9 18 6-6-6', false);
        $response->assertSee('icon-tabler-list-search size-5 text-neutral-700 dark:text-neutral-200', false);
        $response->assertSee('<h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Other sections in '.$ruleset->name.'</h2>', false);
        $response->assertSee('m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400', false);
        $response->assertSeeText('Division B');
        $response->assertDontSeeText('Division Deleted');
        $response->assertDontSee('href="'.route('team.show', $homeTeam->id).'"', false);
        $response->assertSee('data-section-table-row-type="static"', false);
        $response->assertSee('href="'.route('history.section.show', [$season, $ruleset, $otherSection]).'"', false);

        $fixturesResponse = $this->get(route('history.section.show', [
            'season' => $season,
            'ruleset' => $ruleset,
            'section' => $section,
            'tab' => 'fixtures-results',
            'week' => 2,
        ]));

        $fixturesResponse->assertOk();
        $fixturesResponse->assertSeeText('Reds');
        $fixturesResponse->assertSeeText('Blues');
        $fixturesResponse->assertDontSeeText('Modern Reds');
        $fixturesResponse->assertDontSeeText('Modern Blues');
        $fixturesResponse->assertSee('ui-card ui-fixtures-card', false);
        $fixturesResponse->assertSee('ui-fixtures-item-group', false);
        $fixturesResponse->assertSee('ui-fixture-item', false);
        $fixturesResponse->assertDontSeeText('Home vs Away');
        $fixturesResponse->assertDontSee('ui-section-intro-icon', false);
        $fixturesResponse->assertSee('icon-tabler-calendar size-5 text-neutral-700 dark:text-neutral-200', false);
        $fixturesResponse->assertSee('<h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Fixtures & Results</h2>', false);
        $fixturesResponse->assertSee('m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400', false);
        $fixturesResponse->assertSee('href="'.route('result.show', $result).'"', false);

        $averagesResponse = $this->get(route('history.section.show', [
            'season' => $season,
            'ruleset' => $ruleset,
            'section' => $section,
            'tab' => 'averages',
        ]));

        $averagesResponse->assertOk();
        $averagesResponse->assertSeeText('Archived Alice');
        $averagesResponse->assertSee('data-section-averages-row-type="static"', false);
    }

    public function test_history_section_caches_are_invalidated_when_archived_team_or_player_changes(): void
    {
        Cache::flush();

        $ruleset = Ruleset::factory()->create(['name' => 'World Rules']);
        $season = Season::factory()->create([
            'name' => '2021/22 Season',
            'is_open' => false,
            'dates' => [now()->subWeek()->toDateString()],
        ]);
        $section = Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $season->id,
            'name' => 'Division A',
        ]);

        $homeTeam = Team::factory()->create(['name' => 'Reds']);
        $awayTeam = Team::factory()->create(['name' => 'Blues']);

        $section->teams()->attach([
            $homeTeam->id => ['sort' => 1],
            $awayTeam->id => ['sort' => 2],
        ]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'week' => 1,
            'fixture_date' => now()->subWeek(),
        ]);

        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'home_score' => 6,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => 4,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => true,
        ]);

        $homePlayer = User::factory()->create(['name' => 'Archived Alice', 'team_id' => $homeTeam->id]);
        $awayPlayer = User::factory()->create(['name' => 'Active Bob', 'team_id' => $awayTeam->id]);

        Frame::create([
            'result_id' => $result->id,
            'home_player_id' => $homePlayer->id,
            'home_score' => 1,
            'away_player_id' => $awayPlayer->id,
            'away_score' => 0,
        ]);

        $tablesResponse = $this->get(route('history.section.show', [$season, $ruleset, $section]));
        $tablesResponse->assertOk();
        $tablesResponse->assertSee('data-section-table-row-type="link"', false);

        $averagesResponse = $this->get(route('history.section.show', [
            'season' => $season,
            'ruleset' => $ruleset,
            'section' => $section,
            'tab' => 'averages',
        ]));
        $averagesResponse->assertOk();
        $averagesResponse->assertSee('data-section-averages-row-type="link"', false);

        $homeTeam->delete();
        $homePlayer->delete();

        $tablesResponse = $this->get(route('history.section.show', [$season, $ruleset, $section]));
        $tablesResponse->assertOk();
        $tablesResponse->assertSee('data-section-table-row-type="static"', false);

        $averagesResponse = $this->get(route('history.section.show', [
            'season' => $season,
            'ruleset' => $ruleset,
            'section' => $section,
            'tab' => 'averages',
        ]));
        $averagesResponse->assertOk();
        $averagesResponse->assertSee('data-section-averages-row-type="static"', false);
    }

    public function test_history_section_page_livewire_switches_tabs_and_preserves_history_urls(): void
    {
        $ruleset = Ruleset::factory()->create();
        $season = Season::factory()->create([
            'is_open' => false,
            'dates' => [now()->subWeeks(2)->toDateString()],
        ]);
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Division A',
        ]);

        Livewire::test(HistorySectionPage::class, [
            'season' => $season,
            'ruleset' => $ruleset,
            'section' => $section,
            'initialTab' => 'tables',
        ])
            ->assertSet('activeTab', 'tables')
            ->assertSee('data-history-section-page', false)
            ->assertSee('data-ruleset-active-panel="tables"', false)
            ->assertSee('data-section-table-view', false)
            ->call('setActiveTab', 'fixtures-results')
            ->assertSet('activeTab', 'fixtures-results')
            ->assertSee('data-ruleset-active-panel="fixtures-results"', false)
            ->assertSee('data-section-fixtures-view', false)
            ->call('setActiveTab', 'averages')
            ->assertSet('activeTab', 'averages')
            ->assertSee('data-ruleset-active-panel="averages"', false)
            ->assertSee('data-section-averages-view', false)
            ->assertSee(route('history.section.show', [
                'season' => $season,
                'ruleset' => $ruleset,
                'section' => $section,
                'tab' => 'averages',
            ], false), false);
    }
}
