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
use Livewire\Livewire;
use Tests\TestCase;

class HistoryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_index_lists_concluded_seasons_with_rulesets_and_sections_in_accordion_markup(): void
    {
        $ruleset = Ruleset::factory()->create(['name' => 'Eight Ball']);

        $archivedSeason = Season::factory()->create([
            'name' => '2022/23 Season',
            'is_open' => false,
        ]);

        $concludedOpenSeason = Season::factory()->create([
            'name' => '2023/24 Season',
            'is_open' => true,
            'dates' => [now()->subWeek()->toDateString()],
        ]);

        $futureSeason = Season::factory()->create([
            'name' => '2026/27 Season',
            'is_open' => true,
            'dates' => [now()->addMonth()->toDateString()],
        ]);

        $archivedSection = Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $archivedSeason->id,
            'name' => 'Division One',
        ]);

        Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $concludedOpenSeason->id,
            'name' => 'Division Two',
        ]);

        $deletedSection = Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $archivedSeason->id,
            'name' => 'Division Deleted',
        ]);

        $deletedSection->delete();

        $archivedKnockout = Knockout::query()->create([
            'season_id' => $archivedSeason->id,
            'name' => 'Singles Cup',
            'type' => KnockoutType::Singles,
        ]);

        Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $futureSeason->id,
            'name' => 'Future Division',
        ]);

        $response = $this->get(route('history.index'));

        $response->assertOk();
        $response->assertSeeText('History');
        $response->assertSeeText('Season archive');
        $response->assertSeeText('Browse past seasons, then drill into each ruleset and section for archived standings, fixtures, results, and averages.');
        $response->assertSee('ui-section-intro-icon', false);
        $response->assertSee('ui-page-shell', false);
        $response->assertSee('ui-section', false);
        $response->assertSee('ui-shell-grid', false);
        $response->assertSee('ui-card', false);
        $response->assertSee('ui-card-rows', false);
        $response->assertSee('ui-card-row', false);
        $response->assertSee('data-history-index-accordion', false);
        $response->assertSee('data-history-season-trigger', false);
        $response->assertSee('data-history-ruleset-trigger', false);
        $response->assertSee('data-history-section-link', false);
        $response->assertSee('data-history-knockouts-shell', false);
        $response->assertSee('data-history-knockout-link', false);
        $response->assertSeeText('2022/23 Season');
        $response->assertSeeText('2023/24 Season');
        $response->assertSeeText('Eight Ball');
        $response->assertSeeText('Division One');
        $response->assertDontSeeText('Division Deleted');
        $response->assertSeeText('Knockouts');
        $response->assertSeeText('Singles Cup');
        $response->assertSee('href="'.route('history.section.show', [
            'season' => $archivedSeason,
            'ruleset' => $ruleset,
            'section' => $archivedSection,
        ]).'"', false);
        $response->assertSee('href="'.route('history.knockout.show', [
            'season' => $archivedSeason,
            'knockout' => $archivedKnockout,
        ]).'"', false);
        $response->assertDontSeeText('2026/27 Season');
        $response->assertDontSee('href="'.route('history.section.show', [
            'season' => $futureSeason,
            'ruleset' => $ruleset,
            'section' => 'future-division',
        ]).'"', false);
    }

    public function test_history_index_prefers_the_live_section_when_duplicate_historical_section_names_exist(): void
    {
        $ruleset = Ruleset::factory()->create(['name' => 'International Rules']);

        $season = Season::factory()->create([
            'name' => 'February 2024',
            'is_open' => false,
        ]);

        $archivedSection = Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $season->id,
            'name' => 'International Premier',
        ]);

        $archivedSection->delete();

        $liveSection = Section::factory()->create([
            'ruleset_id' => $ruleset->id,
            'season_id' => $season->id,
            'name' => 'International Premier',
        ]);

        $response = $this->get(route('history.index'));

        $response->assertOk();
        $response->assertSee('href="'.route('history.section.show', [
            'season' => $season,
            'ruleset' => $ruleset,
            'section' => $liveSection,
        ]).'"', false);
        $response->assertDontSee('href="'.route('history.section.show', [
            'season' => $season,
            'ruleset' => $ruleset,
            'section' => $archivedSection,
        ]).'"', false);
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
        $response->assertSee('data-ruleset-active-panel="tables"', false);
        $response->assertSee('border-y border-gray-200 bg-white dark:border-neutral-800/80 dark:bg-neutral-900/75', false);
        $response->assertDontSee('sticky top-[72px] z-30 bg-linear-to-br from-green-900 via-green-800 to-green-700 shadow-xl', false);
        $response->assertDontSee('data-section-tab-indicator', false);
        $response->assertSeeText('Division A');
        $response->assertSeeText('2021/22 Season');
        $response->assertSeeText('Reds');
        $response->assertDontSeeText('Modern Reds');
        $response->assertSee('data-section-see-also', false);
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
