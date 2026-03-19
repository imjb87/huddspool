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
            'content' => '<p>World rules guidance.</p>',
        ]);

        $response = $this->get(route('ruleset.show', $ruleset));

        $response->assertOk();
        $response->assertSeeText('International Rules');
        $response->assertSee('data-ruleset-content-page', false);
        $response->assertSee('World rules guidance.', false);
        $response->assertDontSeeLivewire(RulesetSectionPage::class);
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
        $fixturesResponse->assertSee('w-full overflow-hidden border-y border-gray-200 bg-white', false);
        $fixturesResponse->assertSee('bg-linear-to-b from-gray-50 to-gray-100', false);
        $fixturesResponse->assertSee('Print fixtures', false);
        $fixturesResponse->assertSee('Week 1', false);
        $fixturesResponse->assertSee('inline-flex items-center text-gray-700 hover:text-green-800', false);
        $fixturesResponse->assertSee('flex w-[18%] items-center justify-center', false);
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
        $tablesResponse->assertSee('sticky top-[72px] z-30 bg-linear-to-br from-green-900 via-green-800 to-green-700 shadow-xl', false);
        $tablesResponse->assertSee('mx-auto max-w-7xl px-2.5 py-3 lg:px-8', false);
        $tablesResponse->assertSee('data-section-tabs-track', false);
        $tablesResponse->assertSee('data-section-tab-indicator', false);
        $tablesResponse->assertSee('x-init="syncIndicator()"', false);
        $tablesResponse->assertSee("@click=\"syncIndicator('fixtures-results')\"", false);
        $tablesResponse->assertSee('mx-auto w-full max-w-2xl rounded-full bg-black/15 p-1', false);
        $tablesResponse->assertSee('relative grid w-full grid-cols-3 gap-2', false);
        $tablesResponse->assertSee('relative z-10 min-w-0', false);
        $tablesResponse->assertSee('inline-flex min-w-0 w-full items-center justify-center rounded-full', false);
        $tablesResponse->assertSee('px-3 py-2 text-center', false);
        $tablesResponse->assertSee('text-[13px]', false);
        $tablesResponse->assertSee('sm:px-4 sm:text-sm', false);
        $tablesResponse->assertSee('backdrop-blur-xl', false);
        $tablesResponse->assertSee('bg-linear-to-b from-white/90 via-white/70 to-white/50', false);
        $tablesResponse->assertSee('text-green-900', false);
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
        $tablesResponse->assertSee('mx-auto max-w-7xl px-4 sm:px-6 lg:px-8', false);
        $tablesResponse->assertSee('lg:grid-cols-[minmax(0,19rem)_minmax(0,1fr)] lg:items-start lg:gap-12', false);
        $this->assertSame(6, substr_count($tablesResponse->getContent(), 'data-section-sponsors-card'));
        $tablesResponse->assertSeeText('Backing the league every week');
        $tablesResponse->assertSee('w-full overflow-hidden border-y border-gray-200 bg-white', false);
        $tablesResponse->assertSee('bg-linear-to-b from-gray-50 to-gray-100', false);
        $tablesResponse->assertSee('w-[44%] pl-4 sm:w-1/2 sm:pl-6', false);
        $tablesResponse->assertSee('w-[56%] pr-4 sm:w-1/2 sm:pr-0', false);
        $tablesResponse->assertSee('w-1/5 py-2 text-right text-sm font-semibold text-gray-900', false);
        $tablesResponse->assertDontSee('Print fixtures', false);
        $tablesResponse->assertDontSee('Current standings for this section.');
        $tablesResponse->assertDontSee('rounded-2xl border border-gray-200 bg-white shadow-sm', false);
        $tablesResponse->assertDontSee('<h1 class="mt-2 text-lg font-semibold text-white sm:text-xl">Division A</h1>', false);
        $tablesResponse->assertDontSee('data-ruleset-hub', false);
        $tablesResponse->assertDontSee('mb-3 text-xs font-semibold uppercase tracking-[0.25em] text-gray-500">Sections', false);
        $tablesResponse->assertDontSee('mx-auto mt-6 flex max-w-7xl flex-col px-4 lg:px-8', false);

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
        $averagesResponse->assertSee('mx-auto w-full max-w-4xl pl-4 py-4 pr-4 sm:pl-6 sm:pr-6 lg:pl-6 lg:pr-0 lg:py-6', false);
        $averagesResponse->assertSee('flex w-[41%] justify-end', false);
        $averagesResponse->assertSee('flex w-[18%] items-center justify-center', false);
        $averagesResponse->assertSee('cursor-pointer items-center justify-center rounded-full', false);
        $averagesResponse->assertSee('data-section-averages-row-skeleton', false);
        $averagesResponse->assertSee('wire:target="previousPage, nextPage"', false);
        $this->assertSame(10, substr_count($averagesResponse->getContent(), 'data-section-tab-skeleton-row="averages"'));
        $this->assertSame(10, substr_count($averagesResponse->getContent(), 'data-section-averages-row-skeleton-row'));
        $averagesResponse->assertSee('w-full overflow-hidden border-y border-gray-200 bg-white', false);
        $averagesResponse->assertSee('bg-linear-to-b from-gray-50 to-gray-100', false);
        $averagesResponse->assertSee('w-[56%] pl-4 sm:w-1/2 sm:pl-6', false);
        $averagesResponse->assertSee('grid w-[44%] grid-cols-[1fr_1fr_1fr_3.25rem] items-center gap-x-1 pr-4 sm:w-1/2 sm:pr-0', false);
        $averagesResponse->assertSee('text-sm font-semibold text-gray-900', false);
        $averagesResponse->assertSee('bg-linear-to-br from-green-900 via-green-800 to-green-700', false);
        $averagesResponse->assertSee('inline-flex w-24 cursor-pointer items-center justify-center rounded-full', false);
        $averagesResponse->assertSee('Page 1');
        $averagesResponse->assertSee('Previous');
        $averagesResponse->assertSee('Next');
        $averagesResponse->assertDontSee('Print fixtures', false);
        $averagesResponse->assertDontSee('Frame records and win rates for this section.');
        $averagesResponse->assertDontSee('rounded-2xl border border-gray-200 bg-white shadow-sm', false);

    }
}
