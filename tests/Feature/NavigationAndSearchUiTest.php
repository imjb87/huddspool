<?php

namespace Tests\Feature;

use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationAndSearchUiTest extends TestCase
{
    use RefreshDatabase;

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
        $response->assertSee('href="'.route('ruleset.show', $firstRuleset).'"', false);
        $response->assertSee('data-mobile-ruleset-trigger', false);
        $response->assertSee('data-mobile-ruleset-sections', false);
        $response->assertSee('rounded-lg px-3 py-3 text-base font-semibold leading-7 text-gray-900', false);
        $response->assertSee('<span class="fa-stack -ml-1" aria-hidden="true">', false);
        $response->assertDontSee('<a href="#" class="-m-1.5 p-1.5">', false);
        $response->assertDontSee('<a href="/" class="fa-stack -ml-1">', false);
        $response->assertDontSee('id="searchIcon"', false);
        $response->assertDontSee('ring-opacity-5', false);
        $response->assertDontSee('href="'.route('ruleset.index').'"', false);
        $response->assertDontSee('aria-label="Primary mobile"', false);
    }
}
