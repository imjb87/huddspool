<?php

namespace Tests\Feature;

use App\KnockoutType;
use App\Models\Knockout;
use App\Models\Page;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class NavigationAndSearchUiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_home_page_renders_header_and_search_with_tailwind_four_safe_markup(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $firstRuleset = null;

        foreach (['International Rules', 'Blackball Rules', 'EPA Rules'] as $name) {
            $ruleset = Ruleset::factory()->create(['name' => $name]);

            Section::factory()->create([
                'season_id' => $season->id,
                'ruleset_id' => $ruleset->id,
                'name' => $name.' Section 1',
            ]);

            if (! ($firstRuleset instanceof Ruleset)) {
                $firstRuleset = $ruleset;
            }
        }

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('site-header', false);
        $response->assertSee('bg-gray-500/25', false);
        $response->assertSee('bg-gray-500/50', false);
        $response->assertSee('ring-black/5', false);
        $response->assertSee('data-site-search-trigger', false);
        $response->assertSee('focus-first-search-result', false);
        $response->assertSee('Ctrl K', false);
        $response->assertSee('placeholder="Search players, teams, venues..."', false);
        $response->assertSee('pl-11 pr-4', false);
        $response->assertSee('sm:pr-24', false);
        $response->assertSee('<a href="/" class="-m-1.5 p-1.5">', false);
        $response->assertSeeText('International Rules');
        $response->assertSeeText('Blackball Rules');
        $response->assertSeeText('EPA Rules');
        $response->assertSeeText('History');
        $response->assertSeeText('Knockouts');
        $response->assertSeeText('Handbook');
        $response->assertDontSeeText('Ruleset');
        $response->assertSee('href="'.route('ruleset.show', $firstRuleset).'"', false);
        $response->assertSee('href="'.route('home').'"', false);
        $response->assertSee('data-mobile-ruleset-trigger', false);
        $response->assertSee('data-mobile-ruleset-sections', false);
        $response->assertSee('data-mobile-menu-home', false);
        $response->assertSee('data-mobile-menu-close', false);
        $response->assertSee('data-knockouts-nav', false);
        $response->assertSee('data-mobile-knockouts-trigger', false);
        $response->assertSee('data-mobile-knockouts-links', false);
        $response->assertSee('activeAccordion: null', false);
        $response->assertSee('@click="open = false; activeAccordion = null"', false);
        $response->assertSee('@click.stop', false);
        $response->assertSee('rounded-lg px-3 py-3 text-base font-semibold leading-7 text-gray-900', false);
        $response->assertSee('aria-label="Close menu"', false);
        $response->assertSee('<span class="fa-stack -ml-1" aria-hidden="true">', false);
        $response->assertDontSee('<a href="#" class="-m-1.5 p-1.5">', false);
        $response->assertDontSee('<a href="/" class="fa-stack -ml-1">', false);
        $response->assertDontSee('id="searchIcon"', false);
        $response->assertDontSee('ring-opacity-5', false);
        $response->assertDontSee('sectionsOpen:', false);
        $response->assertDontSee('knockoutsOpen:', false);
        $response->assertDontSee('href="'.route('ruleset.index').'"', false);
        $response->assertDontSee('aria-label="Primary mobile"', false);
    }

    public function test_home_page_lists_current_knockouts_and_knockout_dates_in_navigation(): void
    {
        $openSeason = Season::factory()->create(['is_open' => true]);
        $closedSeason = Season::factory()->create(['is_open' => false]);

        $activeKnockout = Knockout::query()->create([
            'season_id' => $openSeason->id,
            'name' => 'Champion of Champions',
            'slug' => 'champion-of-champions',
            'type' => KnockoutType::Singles->value,
        ]);

        Knockout::query()->create([
            'season_id' => $closedSeason->id,
            'name' => 'Archived Knockout',
            'slug' => 'archived-knockout',
            'type' => KnockoutType::Singles->value,
        ]);

        Page::query()->create([
            'title' => 'Knockout Dates',
            'slug' => 'knockout-dates',
            'content' => '<p>Important knockout dates.</p>',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee(route('knockout.show', $activeKnockout), false);
        $response->assertSeeText('Champion of Champions');
        $response->assertSee('href="'.route('page.show', 'knockout-dates').'"', false);
        $response->assertSeeText('Knockout Dates');
        $response->assertDontSeeText('Archived Knockout');
        $response->assertDontSee('href="'.route('knockout.index').'"', false);
    }

    public function test_knockout_index_redirects_to_knockout_dates_page(): void
    {
        $response = $this->get(route('knockout.index'));

        $response->assertRedirect(route('page.show', 'knockout-dates'));
    }

    public function test_home_page_lists_sections_in_navigation_using_section_record_order(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create([
            'name' => 'International Rules',
        ]);

        Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'International Section Two',
        ]);

        Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'International Premier',
        ]);

        Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'International Section One',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSeeInOrder([
            'International Section Two',
            'International Premier',
            'International Section One',
        ]);
    }
}
