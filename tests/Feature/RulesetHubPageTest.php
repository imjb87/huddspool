<?php

namespace Tests\Feature;

use App\Livewire\RulesetSectionPage;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RulesetHubPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_rulesets_index_is_not_defined(): void
    {
        $this->get('/rulesets')
            ->assertNotFound();
    }

    public function test_ruleset_show_renders_ruleset_sections_hub(): void
    {
        $season = Season::factory()->create([
            'is_open' => true,
            'name' => 'Summer 2026',
        ]);
        $ruleset = Ruleset::factory()->create([
            'name' => 'International Rules',
            'slug' => 'international-rules',
            'content' => '<p>World rules guidance.</p>',
        ]);
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Premier Division',
        ]);

        $response = $this->get(route('ruleset.show', $ruleset));

        $response->assertOk();
        $response->assertSeeText('International Rules');
        $response->assertSee('data-ruleset-hub', false);
        $response->assertSee('ui-page-shell', false);
        $response->assertSee('data-section-shared-header', false);
        $response->assertSee('class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100"', false);
        $response->assertDontSee('ui-page-title-icon', false);
        $response->assertSee('data-ruleset-sections', false);
        $response->assertSee('data-ruleset-sections-list', false);
        $response->assertSee('ui-section', false);
        $response->assertSee('icon-tabler-list size-5 text-neutral-700 dark:text-neutral-200', false);
        $response->assertSee('flex size-6 shrink-0 items-center justify-center', false);
        $response->assertSee('font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50', false);
        $response->assertSee('m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400', false);
        $response->assertDontSee('ui-section-intro-icon', false);
        $response->assertSee('ui-card ui-section-see-also-card', false);
        $response->assertSee('ui-section-see-also-list', false);
        $response->assertSee('ui-section-see-also-item', false);
        $response->assertSee('ui-section-see-also-item-action', false);
        $response->assertSee('m9 18 6-6-6', false);
        $response->assertSeeText('Current sections');
        $response->assertSeeText('Premier Division');
        $response->assertSeeText('Summer 2026');
        $response->assertSee('href="'.route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section]).'"', false);
        $response->assertDontSeeLivewire(RulesetSectionPage::class);
        $this->assertSame('/rulesets/international-rules', route('ruleset.show', $ruleset, false));
    }

    public function test_ruleset_rules_route_renders_ruleset_content_page(): void
    {
        $ruleset = Ruleset::factory()->create([
            'name' => 'International Rules',
            'slug' => 'international-rules',
            'content' => '<p>World rules guidance.</p>',
        ]);

        $response = $this->get(route('ruleset.rules', $ruleset));

        $response->assertOk();
        $response->assertSee('data-ruleset-content-page', false);
        $response->assertSee('ui-document-page', false);
        $response->assertSee('data-ui-document-header', false);
        $response->assertSee('ui-document-title', false);
        $response->assertSee('ui-document-card', false);
        $response->assertSee('ui-document-card-body', false);
        $response->assertSee('ui-document-prose', false);
        $response->assertDontSee('ui-page-title-icon', false);
        $response->assertSee('data-ruleset-content-section', false);
        $response->assertSee('data-ruleset-content', false);
        $response->assertSeeText('World rules guidance.');
        $this->assertSame('/rulesets/international-rules/rules', route('ruleset.rules', $ruleset, false));
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
            '/rulesets/blackball-rules/blackball-premier',
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
            '/rulesets/international-rules/premier-division',
            route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $currentSection], false)
        );
    }

    public function test_soft_deleted_section_releases_the_clean_slug_for_replacement(): void
    {
        $season = Season::factory()->create([
            'is_open' => true,
            'slug' => 'summer-2026',
            'dates' => [now()->toDateString()],
        ]);
        $ruleset = Ruleset::factory()->create([
            'slug' => 'international-rules',
        ]);

        $originalSection = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Premier Division',
        ]);

        $originalSection->delete();
        $originalSection->refresh();

        $replacementSection = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Premier Division',
        ]);

        $this->assertSame('premier-division-archived-'.$originalSection->id, $originalSection->slug);
        $this->assertSame('premier-division', $replacementSection->slug);
        $this->assertSame(
            '/rulesets/international-rules/premier-division',
            route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $replacementSection], false)
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

        $response = $this->get('/rulesets/international-rules/premier-division');

        $response->assertOk();
        $response->assertSeeText($ruleset->name);
        $response->assertSeeText($currentSection->name);
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

    public function test_ruleset_hub_uses_canonical_tab_urls(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $this->assertSame(
            "/rulesets/{$ruleset->slug}/{$section->slug}",
            route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section], false)
        );
        $this->assertSame(
            "/rulesets/{$ruleset->slug}/{$section->slug}?tab=fixtures-results",
            route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section, 'tab' => 'fixtures-results'], false)
        );
        $this->assertSame(
            "/rulesets/{$ruleset->slug}/{$section->slug}?tab=averages",
            route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section, 'tab' => 'averages'], false)
        );
    }

    public function test_ruleset_show_renders_empty_state_without_open_sections(): void
    {
        $ruleset = Ruleset::factory()->create([
            'content' => null,
        ]);

        $response = $this->get(route('ruleset.show', $ruleset));

        $response->assertOk();
        $response->assertSee('ui-page-shell', false);
        $response->assertSee('data-ruleset-sections-empty', false);
        $response->assertSee('ui-section', false);
        $response->assertSeeText('This ruleset has no open sections yet.');
    }

    public function test_ruleset_rules_route_renders_empty_state_without_content(): void
    {
        $ruleset = Ruleset::factory()->create([
            'content' => null,
        ]);

        $response = $this->get(route('ruleset.rules', $ruleset));

        $response->assertOk();
        $response->assertSee('data-ruleset-content-empty', false);
        $response->assertSee('ui-document-empty', false);
        $response->assertSee('text-sm leading-5 font-medium text-foreground', false);
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

    public function test_switching_to_fixtures_results_uses_previous_scheduled_week_during_a_week_off(): void
    {
        $season = Season::factory()->create([
            'is_open' => true,
            'dates' => [
                now()->copy()->subWeeks(2)->toDateString(),
                now()->copy()->subWeek()->toDateString(),
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
        $fixturesResponse->assertSee('class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100"', false);
        $fixturesResponse->assertDontSee('data-section-header-icon', false);
        $fixturesResponse->assertSee(route('ruleset.show', $ruleset), false);
        $fixturesResponse->assertSeeText('Division A');
        $fixturesResponse->assertSee('data-ruleset-active-panel="fixtures-results"', false);
        $fixturesResponse->assertSee('data-section-tab-skeleton', false);
        $fixturesResponse->assertSee('data-section-tab-skeleton="fixtures-results"', false);
        $fixturesResponse->assertSee('data-section-fixtures-view', false);
        $fixturesResponse->assertSee('data-section-fixtures-shell', false);
        $fixturesResponse->assertSee('bg-card text-card-foreground ring-1 ring-foreground/10', false);
        $fixturesResponse->assertDontSee('ui-section-intro-icon', false);
        $fixturesResponse->assertSee('flex size-6 shrink-0 items-center justify-center', false);
        $fixturesResponse->assertSee('icon-tabler-calendar size-5 text-neutral-700 dark:text-neutral-200', false);
        $fixturesResponse->assertSee('ui-section-intro-copy grid auto-rows-min items-start gap-1.5', false);
        $fixturesResponse->assertDontSee('data-section-fixtures-headings', false);
        $fixturesResponse->assertSee('ui-card ui-fixtures-card', false);
        $fixturesResponse->assertSee('ui-fixtures-item-group', false);
        $fixturesResponse->assertSee('ui-fixture-item', false);
        $fixturesResponse->assertSee('ui-fixture-team-names', false);
        $fixturesResponse->assertSee('data-section-fixtures-band', false);
        $fixturesResponse->assertSee('data-section-fixtures-controls', false);
        $fixturesResponse->assertSee('data-section-fixtures-row-skeleton', false);
        $fixturesResponse->assertSee('data-section-fixtures-date-skeleton', false);
        $fixturesResponse->assertSee('wire:target="previousWeek, nextWeek"', false);
        $fixtureDocument = new \DOMDocument();
        $fixtureDocument->loadHTML($fixturesResponse->getContent(), LIBXML_NONET);
        $fixtureXPath = new \DOMXPath($fixtureDocument);
        $this->assertSame(1, $fixtureXPath->query('//*[@data-section-fixtures-header]//*[@data-section-fixtures-date-skeleton]')->length);
        $this->assertSame(0, $fixtureXPath->query('//*[@data-section-fixtures-row-skeleton]//*[@data-section-fixtures-date-skeleton]')->length);
        $this->assertSame(5, substr_count($fixturesResponse->getContent(), 'data-section-tab-skeleton-row="fixtures-results"'));
        $this->assertSame(5, substr_count($fixturesResponse->getContent(), 'data-section-fixtures-row-skeleton-row'));
        $fixturesResponse->assertSee('ui-shell-grid', false);
        $fixturesResponse->assertSee('<h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Fixtures & Results</h2>', false);
        $fixturesResponse->assertSee('m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400', false);
        $fixturesResponse->assertSee('Print fixtures', false);
        $fixturesResponse->assertSee('data-section-fixtures-header', false);
        $fixturesResponse->assertSee('border-b border-border px-5 py-4', false);
        $fixturesResponse->assertSee('data-section-fixtures-print', false);
        $fixturesResponse->assertSeeText('Print');
        $fixturesResponse->assertSee('Week 1', false);
        $fixturesResponse->assertDontSeeText('Home vs Away');
        $fixturesResponse->assertSee('ui-tab-trigger min-w-24 gap-2', false);
        $fixturesResponse->assertSee('icon-tabler-printer size-4', false);
        $fixturesResponse->assertSee('data-section-fixtures-pagination', false);
        $fixturesResponse->assertSee('ui-fixtures-pagination', false);
        $fixturesResponse->assertSee('ui-pagination-link rounded-full', false);
        $fixturesResponse->assertSee('ui-pagination-current', false);
        $fixturesResponse->assertSee('icon-tabler-chevron-left size-4', false);
        $fixturesResponse->assertSee('icon-tabler-chevron-right size-4', false);
        $fixturesResponse->assertDontSee('ui-button-primary min-w-24', false);
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
        $tablesResponse->assertSee('class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100"', false);
        $tablesResponse->assertDontSee('data-section-header-icon', false);
        $tablesResponse->assertSee(route('ruleset.show', $ruleset), false);
        $tablesResponse->assertSeeText('Division A');
        $tablesResponse->assertSee('data-section-tabs', false);
        $tablesResponse->assertSee('data-section-tabs-scroll', false);
        $tablesResponse->assertSee('data-active-section-tab="tables"', false);
        $tablesResponse->assertSee('data-section-tab="fixtures-results"', false);
        $tablesResponse->assertSee('tabindex="0"', false);
        $tablesResponse->assertSee('aria-label="Section tabs"', false);
        $tablesResponse->assertSee('aria-current="page"', false);
        $tablesResponse->assertSee('wire:click.prevent="setActiveTab(\'fixtures-results\')"', false);
        $tablesResponse->assertSee('wire:target="setActiveTab(\'tables\')"', false);
        $tablesResponse->assertSee('wire:target="setActiveTab(\'fixtures-results\')"', false);
        $tablesResponse->assertSee('wire:target="setActiveTab(\'averages\')"', false);
        $tablesResponse->assertSee('data-section-tab-skeleton', false);
        $tablesResponse->assertSee('data-section-tab-skeleton="tables"', false);
        $this->assertSame(10, substr_count($tablesResponse->getContent(), 'data-section-tab-skeleton-row="tables"'));
        $tablesResponse->assertSee('ui-tab-strip-shell', false);
        $tablesResponse->assertSee('ui-tab-strip', false);
        $tablesResponse->assertSee('snap-start', false);
        $tablesResponse->assertSee('data-section-tabs-track', false);
        $tablesResponse->assertSee('ui-tab-trigger', false);
        $tablesResponse->assertSee('data-state="active"', false);
        $tablesResponse->assertSee('ui-page-shell', false);
        $tablesResponse->assertSee('data-ruleset-active-panel="tables"', false);
        $tablesResponse->assertSee('data-section-table-view', false);
        $tablesResponse->assertSee('icon-tabler-list-numbers size-5 text-neutral-700 dark:text-neutral-200', false);
        $tablesResponse->assertSee('flex size-6 shrink-0 items-center justify-center', false);
        $tablesResponse->assertSee('ui-section-intro-copy grid auto-rows-min items-start gap-1.5', false);
        $tablesResponse->assertSee('m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400', false);
        $tablesResponse->assertSee('data-section-table-shell', false);
        $tablesResponse->assertSee('ui-card ui-standings-card', false);
        $tablesResponse->assertSee('ui-standings-item-group', false);
        $tablesResponse->assertSee('ui-standings-item ui-standings-item-header', false);
        $tablesResponse->assertSee('ui-standings-item', false);
        $tablesResponse->assertSee('data-slot="item-content"', false);
        $tablesResponse->assertSee('ui-standings-item-stats', false);
        $tablesResponse->assertDontSee('ui-card-row relative gap-2 px-4 sm:gap-3 sm:px-5', false);
        $tablesResponse->assertSee('data-section-table-band', false);
        $tablesResponse->assertSee('data-section-see-also', false);
        $tablesResponse->assertSee('data-section-see-also-links', false);
        $tablesResponse->assertSee('ui-card ui-section-see-also-card', false);
        $tablesResponse->assertSee('ui-section-see-also-list', false);
        $tablesResponse->assertSee('ui-section-see-also-item', false);
        $tablesResponse->assertSee('data-size="sm"', false);
        $tablesResponse->assertSee('ui-section-see-also-item-action', false);
        $tablesResponse->assertSee('m9 18 6-6-6', false);
        $tablesResponse->assertSee('icon-tabler-list-search size-5 text-neutral-700 dark:text-neutral-200', false);
        $tablesResponse->assertSee('<h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Other sections in '.$ruleset->name.'</h2>', false);
        $tablesResponse->assertSee('m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400', false);
        $tablesResponse->assertSeeText('Other sections in '.$ruleset->name);
        $tablesResponse->assertSeeText('Division B');
        $tablesResponse->assertSee('href="'.route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => Section::query()->where('name', 'Division B')->firstOrFail()]).'"', false);
        $tablesResponse->assertSee('data-section-sponsors', false);
        $tablesResponse->assertSee('data-section-sponsors-carousel', false);
        $tablesResponse->assertSee('mx-auto max-w-4xl px-4 sm:px-6 lg:px-6', false);
        $tablesResponse->assertSee('ui-shell-grid', false);
        $tablesResponse->assertSee('ui-card', false);
        $this->assertSame(6, substr_count($tablesResponse->getContent(), 'data-section-sponsors-card'));
        $tablesResponse->assertSeeText('Backing the league every week');
        $tablesResponse->assertSee('ui-shell-grid', false);
        $tablesResponse->assertSeeText('Standings');
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
        $averagesResponse->assertSee('class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100"', false);
        $averagesResponse->assertDontSee('data-section-header-icon', false);
        $averagesResponse->assertSee(route('ruleset.show', $ruleset), false);
        $averagesResponse->assertSeeText('Division A');
        $averagesResponse->assertSee('data-ruleset-active-panel="averages"', false);
        $averagesResponse->assertSee('ui-page-shell', false);
        $averagesResponse->assertSee('data-section-tab-skeleton', false);
        $averagesResponse->assertSee('data-section-tab-skeleton="averages"', false);
        $averagesResponse->assertSee('data-section-averages-view', false);
        $averagesResponse->assertDontSee('ui-section-intro-icon', false);
        $averagesResponse->assertSee('flex size-6 shrink-0 items-center justify-center', false);
        $averagesResponse->assertSee('font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50', false);
        $averagesResponse->assertSee('data-section-averages-shell', false);
        $averagesResponse->assertSee('data-section-averages-band', false);
        $averagesResponse->assertSee('data-section-averages-controls', false);
        $averagesResponse->assertSee('ui-averages-card', false);
        $averagesResponse->assertSee('ui-average-item ui-average-item-header', false);
        $averagesResponse->assertSee('ui-card-column-header w-12 sm:w-16', false);
        $averagesResponse->assertSee('ui-card-column-header hidden w-12 sm:block sm:w-16', false);
        $averagesResponse->assertSee('hidden w-12 sm:block sm:w-16', false);
        $averagesResponse->assertSee('data-section-see-also', false);
        $averagesResponse->assertSee('href="'.route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => Section::query()->where('name', 'Division B')->firstOrFail(), 'tab' => 'averages']).'"', false);
        $averagesResponse->assertSee('ui-shell-grid', false);
        $averagesResponse->assertSeeText('Averages');
        $averagesResponse->assertSee('ui-averages-item-group', false);
        $averagesResponse->assertSee('ui-average-item', false);
        $averagesResponse->assertSee('data-slot="item-content"', false);
        $averagesResponse->assertSee('ui-average-item-stats', false);
        $averagesResponse->assertDontSee('ui-card-row items-center px-4 sm:px-5', false);
        $averagesResponse->assertSee('data-section-averages-row-skeleton', false);
        $averagesResponse->assertSee('wire:target="previousPage, nextPage"', false);
        $this->assertSame(10, substr_count($averagesResponse->getContent(), 'data-section-tab-skeleton-row="averages"'));
        $this->assertSame(10, substr_count($averagesResponse->getContent(), 'data-section-averages-row-skeleton-row'));
        $averagesResponse->assertSee('data-section-averages-pagination', false);
        $averagesResponse->assertSee('ui-pagination', false);
        $averagesResponse->assertSee('ui-pagination-link', false);
        $averagesResponse->assertSee('icon-tabler-chevron-left size-4', false);
        $averagesResponse->assertSee('icon-tabler-chevron-right size-4', false);
        $averagesResponse->assertDontSee('ui-button-primary min-w-24', false);
        $averagesResponse->assertSee('Page 1');
        $averagesResponse->assertSee('Previous');
        $averagesResponse->assertSee('Next');
        $averagesResponse->assertDontSee('Print fixtures', false);
        $averagesResponse->assertDontSee('Frame records and win rates for this section.');
        $averagesResponse->assertDontSee('rounded-2xl border border-gray-200 bg-white shadow-sm', false);

    }

    public function test_standings_rows_render_top_and_bottom_inset_accents(): void
    {
        $season = Season::factory()->create(['is_open' => true, 'dates' => [now()->toDateString()]]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Division A',
        ]);

        $teams = Team::factory()->count(4)->create();

        $section->teams()->attach(
            $teams->mapWithKeys(fn (Team $team, int $index) => [$team->id => ['sort' => $index + 1]])->all()
        );

        $response = $this->get(route('ruleset.section.show', [
            'ruleset' => $ruleset,
            'section' => $section,
        ]));

        $response->assertOk();
        $this->assertSame(2, substr_count($response->getContent(), 'bg-emerald-500 dark:bg-emerald-400'));
        $this->assertSame(2, substr_count($response->getContent(), 'bg-rose-500 dark:bg-rose-400'));
    }
}
