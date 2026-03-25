<?php

namespace Tests\Feature;

use App\Livewire\RulesetSectionPage;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RulesetHubPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_rulesets_index_redirects_home(): void
    {
        $this->get(route('ruleset.index'))
            ->assertRedirect(route('home'));
    }

    public function test_ruleset_show_renders_ruleset_content_page(): void
    {
        $ruleset = Ruleset::factory()->create([
            'name' => 'International Rules',
            'slug' => 'international-rules',
            'content' => '<p>World rules guidance.</p>',
        ]);

        $response = $this->get(route('ruleset.show', $ruleset));

        $response->assertOk();
        $response->assertSeeText('International Rules');
        $response->assertSee('data-ruleset-content-page', false);
        $response->assertSee('data-section-shared-header', false);
        $response->assertSee('data-ruleset-content-section', false);
        $response->assertSee('dark:bg-zinc-900', false);
        $response->assertSee('dark:text-gray-100', false);
        $response->assertSee('dark:prose-invert', false);
        $response->assertSee('World rules guidance.', false);
        $response->assertDontSeeLivewire(RulesetSectionPage::class);
        $this->assertSame('/international-rules', route('ruleset.show', $ruleset, false));
    }

    public function test_legacy_ruleset_show_route_redirects_to_canonical_ruleset_url(): void
    {
        $ruleset = Ruleset::factory()->create([
            'slug' => 'international-rules',
        ]);

        $this->get("/rulesets/{$ruleset->slug}?tab=fixtures-results")
            ->assertRedirect(route('ruleset.show', [
                'ruleset' => $ruleset,
                'tab' => 'fixtures-results',
            ]));
    }

    public function test_ruleset_section_route_uses_section_slug(): void
    {
        $season = Season::factory()->create(['is_open' => true, 'dates' => [now()->toDateString()]]);
        $ruleset = Ruleset::factory()->create([
            'slug' => 'blackball-rules',
        ]);
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Blackball Premier',
        ]);

        $this->assertSame(
            '/blackball-rules/blackball-premier',
            route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section], false)
        );
    }

    public function test_same_section_name_in_a_new_season_keeps_the_same_slug(): void
    {
        $closedSeason = Season::factory()->create([
            'is_open' => false,
            'slug' => 'winter-2025',
        ]);
        $openSeason = Season::factory()->create([
            'is_open' => true,
            'slug' => 'summer-2026',
            'dates' => [now()->toDateString()],
        ]);
        $ruleset = Ruleset::factory()->create([
            'slug' => 'international-rules',
        ]);

        $archivedSection = Section::factory()->create([
            'season_id' => $closedSeason->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Premier Division',
        ]);
        $currentSection = Section::factory()->create([
            'season_id' => $openSeason->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Premier Division',
        ]);

        $this->assertSame('premier-division', $archivedSection->slug);
        $this->assertSame('premier-division', $currentSection->slug);
        $this->assertSame(
            '/international-rules/premier-division',
            route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $currentSection], false)
        );
    }

    public function test_current_ruleset_section_route_resolves_the_open_season_when_history_uses_the_same_slug(): void
    {
        $closedSeason = Season::factory()->create([
            'is_open' => false,
            'slug' => 'winter-2025',
        ]);
        $openSeason = Season::factory()->create([
            'is_open' => true,
            'slug' => 'summer-2026',
            'dates' => [now()->toDateString()],
        ]);
        $ruleset = Ruleset::factory()->create([
            'slug' => 'international-rules',
        ]);

        Section::factory()->create([
            'season_id' => $closedSeason->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Premier Division',
        ]);
        $currentSection = Section::factory()->create([
            'season_id' => $openSeason->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Premier Division',
        ]);

        $response = $this->get('/international-rules/premier-division');

        $response->assertOk();
        $response->assertSeeText($openSeason->name);
        $response->assertSeeText($currentSection->name);
    }

    public function test_legacy_ruleset_section_route_redirects_to_canonical_section_url(): void
    {
        $season = Season::factory()->create(['is_open' => true, 'dates' => [now()->toDateString()]]);
        $ruleset = Ruleset::factory()->create([
            'slug' => 'international-rules',
        ]);
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'International Premier',
        ]);

        $this->get("/rulesets/{$ruleset->slug}/{$section->slug}?tab=fixtures-results")
            ->assertRedirect(route('ruleset.section.show', [
                'ruleset' => $ruleset,
                'section' => $section,
                'tab' => 'fixtures-results',
            ]));
    }

    public function test_fixture_download_route_is_scoped_by_ruleset_and_section_slug(): void
    {
        $season = Season::factory()->create(['is_open' => true, 'dates' => [now()->toDateString()]]);
        $ruleset = Ruleset::factory()->create([
            'slug' => 'international-rules',
        ]);
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Premier Division',
        ]);

        $this->assertSame(
            '/fixtures/download/international-rules/premier-division',
            route('fixture.download', ['ruleset' => $ruleset, 'section' => $section], false)
        );
    }

    public function test_ruleset_show_respects_tab_and_section_query_parameters(): void
    {
        $season = Season::factory()->create(['is_open' => true, 'dates' => [now()->toDateString()]]);
        $ruleset = Ruleset::factory()->create();
        Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Division A',
        ]);
        $selectedSection = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Division B',
        ]);

        $response = $this->get(route('ruleset.show', [
            'ruleset' => $ruleset,
            'tab' => 'fixtures-results',
            'section' => $selectedSection->id,
        ]));

        $response->assertRedirect(route('ruleset.section.show', [
            'ruleset' => $ruleset,
            'section' => $selectedSection,
            'tab' => 'fixtures-results',
        ]));
    }

    public function test_ruleset_show_still_accepts_legacy_section_id_query_parameter(): void
    {
        $season = Season::factory()->create(['is_open' => true, 'dates' => [now()->toDateString()]]);
        $ruleset = Ruleset::factory()->create();
        $selectedSection = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Division B',
        ]);

        $response = $this->get(route('ruleset.show', [
            'ruleset' => $ruleset,
            'tab' => 'fixtures-results',
            'section' => $selectedSection->id,
        ]));

        $response->assertRedirect(route('ruleset.section.show', [
            'ruleset' => $ruleset,
            'section' => $selectedSection,
            'tab' => 'fixtures-results',
        ]));
    }

    public function test_legacy_metric_routes_redirect_to_canonical_hub_tabs(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $this->get(route('table.index', $ruleset))
            ->assertRedirect(route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section]));

        $this->get(route('fixture.index', $ruleset))
            ->assertRedirect(route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section, 'tab' => 'fixtures-results']));

        $this->get(route('player.index', $ruleset))
            ->assertRedirect(route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section, 'tab' => 'averages']));
    }

    public function test_ruleset_show_renders_empty_state_without_content(): void
    {
        $ruleset = Ruleset::factory()->create([
            'content' => null,
        ]);

        $response = $this->get(route('ruleset.show', $ruleset));

        $response->assertOk();
        $response->assertSee('data-ruleset-content-empty', false);
        $response->assertSee('dark:prose-invert', false);
        $response->assertSeeText('No ruleset content has been published yet.');
    }

    public function test_livewire_section_page_switches_tabs_without_a_full_page_visit(): void
    {
        $season = Season::factory()->create(['is_open' => true, 'dates' => [now()->toDateString()]]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Division A',
        ]);

        Livewire::test(RulesetSectionPage::class, [
            'ruleset' => $ruleset,
            'section' => $section,
            'initialTab' => 'tables',
        ])
            ->assertSet('activeTab', 'tables')
            ->assertSee('data-ruleset-active-panel="tables"', false)
            ->assertSee('data-section-table-view', false)
            ->assertDontSee('data-section-fixtures-view', false)
            ->assertDontSee('data-section-averages-view', false)
            ->assertSee('data-section-tab-skeleton', false)
            ->call('setActiveTab', 'fixtures-results')
            ->assertSet('activeTab', 'fixtures-results')
            ->assertSee('data-ruleset-active-panel="fixtures-results"', false)
            ->assertSee('data-section-fixtures-view', false)
            ->assertDontSee('data-section-table-view', false)
            ->assertDontSee('data-section-averages-view', false)
            ->call('setActiveTab', 'averages')
            ->assertSet('activeTab', 'averages')
            ->assertSee('data-ruleset-active-panel="averages"', false)
            ->assertSee('data-section-averages-view', false)
            ->assertDontSee('data-section-table-view', false)
            ->assertDontSee('data-section-fixtures-view', false);
    }

    public function test_switching_to_fixtures_results_recalculates_the_current_week(): void
    {
        $season = Season::factory()->create([
            'is_open' => true,
            'dates' => [
                now()->copy()->subWeek()->toDateString(),
                now()->toDateString(),
                now()->copy()->addWeek()->toDateString(),
            ],
        ]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Division A',
        ]);

        Livewire::test(RulesetSectionPage::class, [
            'ruleset' => $ruleset,
            'section' => $section,
            'initialTab' => 'tables',
        ])
            ->set('week', 1)
            ->call('setActiveTab', 'fixtures-results')
            ->assertSet('activeTab', 'fixtures-results')
            ->assertSet('week', 2);
    }

    public function test_section_page_mounts_only_the_requested_tab_component(): void
    {
        $season = Season::factory()->create(['is_open' => true, 'dates' => [now()->toDateString()]]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Division A',
        ]);
        Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Division B',
        ]);

        $fixturesResponse = $this->get(route('ruleset.section.show', [
            'ruleset' => $ruleset,
            'section' => $section,
            'tab' => 'fixtures-results',
        ]));

        $fixturesResponse->assertOk();
        $fixturesResponse->assertSeeLivewire(RulesetSectionPage::class);
        $fixturesResponse->assertSee('data-section-shared-header', false);
        $fixturesResponse->assertSeeText('Division A');
        $fixturesResponse->assertSee('data-ruleset-active-panel="fixtures-results"', false);
        $fixturesResponse->assertSee('data-section-tab-skeleton', false);
        $fixturesResponse->assertSee('data-section-tab-skeleton="fixtures-results"', false);
        $fixturesResponse->assertSee('data-section-fixtures-view', false);
        $fixturesResponse->assertSee('data-section-fixtures-shell', false);
        $fixturesResponse->assertSee('data-section-fixtures-band', false);
        $fixturesResponse->assertSee('data-section-fixtures-controls', false);
        $fixturesResponse->assertSee('data-section-fixtures-row-skeleton', false);
        $fixturesResponse->assertSee('wire:target="previousWeek, nextWeek"', false);
        $this->assertSame(5, substr_count($fixturesResponse->getContent(), 'data-section-tab-skeleton-row="fixtures-results"'));
        $this->assertSame(5, substr_count($fixturesResponse->getContent(), 'data-section-fixtures-row-skeleton-row'));
        $fixturesResponse->assertSee('grid gap-8 lg:grid-cols-3 lg:gap-10', false);
        $fixturesResponse->assertSee('<h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Fixtures & Results</h2>', false);
        $fixturesResponse->assertSee('Print fixtures', false);
        $fixturesResponse->assertSeeText('Print');
        $fixturesResponse->assertSee('Week 1', false);
        $fixturesResponse->assertSee('inline-flex min-w-24 items-center justify-center gap-2 self-end rounded-full border border-gray-200 bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:border-gray-300 hover:bg-gray-200/70 hover:text-gray-900', false);
        $fixturesResponse->assertSee('flex items-center justify-between gap-4', false);
        $fixturesResponse->assertSee('text-sm font-semibold text-gray-900', false);
        $fixturesResponse->assertSee('bg-linear-to-br from-green-900 via-green-800 to-green-700', false);
        $fixturesResponse->assertSee('inline-flex w-24 cursor-pointer items-center justify-center rounded-full', false);
        $fixturesResponse->assertSee('Previous');
        $fixturesResponse->assertSee('Next');
        $fixturesResponse->assertDontSee('Week 1 fixtures and results for this section.');
        $fixturesResponse->assertDontSee('>Week 1<', false);
        $fixturesResponse->assertDontSee('&laquo; Previous', false);
        $fixturesResponse->assertDontSee('Next &raquo;', false);
        $fixturesResponse->assertDontSee('rounded-2xl border border-gray-200 bg-white shadow-sm', false);

        $tablesResponse = $this->get(route('ruleset.section.show', [
            'ruleset' => $ruleset,
            'section' => $section,
        ]));

        $tablesResponse->assertOk();
        $tablesResponse->assertSeeLivewire(RulesetSectionPage::class);
        $tablesResponse->assertSee('data-section-shared-header', false);
        $tablesResponse->assertSeeText('Division A');
        $tablesResponse->assertSee('data-section-tabs', false);
        $tablesResponse->assertSee('data-section-tabs-scroll', false);
        $tablesResponse->assertSee('data-active-section-tab="tables"', false);
        $tablesResponse->assertSee('data-section-tab="fixtures-results"', false);
        $tablesResponse->assertSee('aria-current="page"', false);
        $tablesResponse->assertSee('wire:click.prevent="setActiveTab(\'fixtures-results\')"', false);
        $tablesResponse->assertSee('wire:target="setActiveTab(\'tables\')"', false);
        $tablesResponse->assertSee('wire:target="setActiveTab(\'fixtures-results\')"', false);
        $tablesResponse->assertSee('wire:target="setActiveTab(\'averages\')"', false);
        $tablesResponse->assertSee('data-section-tab-skeleton', false);
        $tablesResponse->assertSee('data-section-tab-skeleton="tables"', false);
        $this->assertSame(10, substr_count($tablesResponse->getContent(), 'data-section-tab-skeleton-row="tables"'));
        $tablesResponse->assertSee('border-y border-gray-200 bg-white dark:border-zinc-800/80 dark:bg-zinc-800/75', false);
        $tablesResponse->assertSee('mx-auto flex w-full max-w-4xl gap-2 overflow-x-auto px-4 py-3 sm:px-6 lg:px-6', false);
        $tablesResponse->assertSee('data-section-tabs-track', false);
        $tablesResponse->assertSee('inline-flex shrink-0 items-center rounded-full px-3 py-2 text-sm font-semibold transition', false);
        $tablesResponse->assertSee('bg-gray-100 text-gray-700 dark:bg-zinc-700 dark:text-gray-300', false);
        $tablesResponse->assertSee('pt-[72px] pb-8 lg:pb-8', false);
        $tablesResponse->assertSee('data-ruleset-active-panel="tables"', false);
        $tablesResponse->assertSee('data-section-table-view', false);
        $tablesResponse->assertSee('data-section-table-shell', false);
        $tablesResponse->assertSee('data-section-table-band', false);
        $tablesResponse->assertSee('data-section-see-also', false);
        $tablesResponse->assertSee('data-section-see-also-links', false);
        $tablesResponse->assertSeeText('Other sections in '.$ruleset->name);
        $tablesResponse->assertSeeText('Division B');
        $tablesResponse->assertSee('href="'.route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => Section::query()->where('name', 'Division B')->firstOrFail()]).'"', false);
        $tablesResponse->assertSee('data-section-sponsors', false);
        $tablesResponse->assertSee('data-section-sponsors-grid', false);
        $tablesResponse->assertSee('mx-auto max-w-4xl px-4 sm:px-6 lg:px-6', false);
        $tablesResponse->assertSee('grid gap-8 py-8 sm:py-10 lg:grid-cols-3 lg:gap-10', false);
        $this->assertSame(6, substr_count($tablesResponse->getContent(), 'data-section-sponsors-card'));
        $tablesResponse->assertSeeText('Backing the league every week');
        $tablesResponse->assertSee('grid gap-8 lg:grid-cols-3 lg:gap-10', false);
        $tablesResponse->assertSeeText('Standings');
        $tablesResponse->assertSee('ml-auto grid shrink-0 grid-cols-5 gap-2 text-center sm:gap-3', false);
        $tablesResponse->assertDontSee('Print fixtures', false);
        $tablesResponse->assertDontSee('Current standings for this section.');
        $tablesResponse->assertDontSee('rounded-2xl border border-gray-200 bg-white shadow-sm', false);
        $tablesResponse->assertDontSee('<h1 class="mt-2 text-lg font-semibold text-white sm:text-xl">Division A</h1>', false);
        $tablesResponse->assertDontSee('data-ruleset-hub', false);
        $tablesResponse->assertDontSee('mb-3 text-xs font-semibold uppercase tracking-[0.25em] text-gray-500">Sections', false);
        $tablesResponse->assertDontSee('mx-auto mt-6 flex max-w-7xl flex-col px-4 lg:px-8', false);
        $tablesResponse->assertDontSee('sticky top-[72px] z-30 bg-linear-to-br from-green-900 via-green-800 to-green-700 shadow-xl', false);
        $tablesResponse->assertDontSee('data-section-tab-indicator', false);

        $averagesResponse = $this->get(route('ruleset.section.show', [
            'ruleset' => $ruleset,
            'section' => $section,
            'tab' => 'averages',
        ]));

        $averagesResponse->assertOk();
        $averagesResponse->assertSeeLivewire(RulesetSectionPage::class);
        $averagesResponse->assertSee('data-section-shared-header', false);
        $averagesResponse->assertSeeText('Division A');
        $averagesResponse->assertSee('data-ruleset-active-panel="averages"', false);
        $averagesResponse->assertSee('pt-[72px] pb-8 lg:pb-8', false);
        $averagesResponse->assertSee('data-section-tab-skeleton', false);
        $averagesResponse->assertSee('data-section-tab-skeleton="averages"', false);
        $averagesResponse->assertSee('data-section-averages-view', false);
        $averagesResponse->assertSee('data-section-averages-shell', false);
        $averagesResponse->assertSee('data-section-averages-band', false);
        $averagesResponse->assertSee('data-section-averages-controls', false);
        $averagesResponse->assertSee('data-section-see-also', false);
        $averagesResponse->assertSee('href="'.route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => Section::query()->where('name', 'Division B')->firstOrFail(), 'tab' => 'averages']).'"', false);
        $averagesResponse->assertSee('grid gap-8 lg:grid-cols-3 lg:gap-10', false);
        $averagesResponse->assertSeeText('Averages');
        $averagesResponse->assertSee('flex items-center gap-3 rounded-lg py-3 sm:gap-4 sm:px-3 sm:py-4', false);
        $averagesResponse->assertSee('ml-auto flex shrink-0 items-start gap-2 text-center sm:gap-5', false);
        $averagesResponse->assertSee('data-section-averages-row-skeleton', false);
        $averagesResponse->assertSee('wire:target="previousPage, nextPage"', false);
        $this->assertSame(5, substr_count($averagesResponse->getContent(), 'data-section-tab-skeleton-row="averages"'));
        $this->assertSame(5, substr_count($averagesResponse->getContent(), 'data-section-averages-row-skeleton-row'));
        $averagesResponse->assertSee('inline-flex w-24 cursor-pointer items-center justify-center rounded-full', false);
        $averagesResponse->assertSee('Page 1');
        $averagesResponse->assertSee('Previous');
        $averagesResponse->assertSee('Next');
        $averagesResponse->assertDontSee('Print fixtures', false);
        $averagesResponse->assertDontSee('Frame records and win rates for this section.');
        $averagesResponse->assertDontSee('rounded-2xl border border-gray-200 bg-white shadow-sm', false);

    }
}
