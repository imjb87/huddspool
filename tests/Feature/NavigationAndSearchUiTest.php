<?php

namespace Tests\Feature;

use App\KnockoutType;
use App\Models\Knockout;
use App\Models\Page;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\User;
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
        $response->assertSee('bg-gray-500/40', false);
        $response->assertSee('ring-black/5', false);
        $response->assertSee('data-site-search-trigger', false);
        $response->assertSee('data-theme-toggle', false);
        $response->assertSee('focus-first-search-result', false);
        $response->assertSee('site-theme', false);
        $response->assertSee('prefers-color-scheme: dark', false);
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
        $response->assertSee('<a href="/" class="-m-1.5 p-1.5">', false);
        $response->assertSee('data-mobile-menu-toggle', false);
        $response->assertSee('data-mobile-menu-drawer', false);
        $response->assertSee('data-mobile-menu-panel="root"', false);
        $response->assertSee('data-mobile-ruleset-trigger', false);
        $response->assertSee('data-mobile-ruleset-sections', false);
        $response->assertSee('data-knockouts-nav', false);
        $response->assertSee('data-mobile-history-trigger', false);
        $response->assertSee('data-mobile-history-links', false);
        $response->assertSee('data-mobile-knockouts-trigger', false);
        $response->assertSee('data-mobile-knockouts-links', false);
        $response->assertSee('data-mobile-back-label', false);
        $response->assertSee("activeDrawer: 'root'", false);
        $response->assertSee('open ? closeMenu() : openMenu()', false);
        $response->assertSee("\$watch('open', value => document.body.classList.toggle('overflow-hidden', value))", false);
        $response->assertSee('deferredInstallPrompt: null', false);
        $response->assertSee('canInstallApp: false', false);
        $response->assertSee("window.addEventListener('beforeinstallprompt', event => { event.preventDefault(); deferredInstallPrompt = event; syncInstallAvailability(); });", false);
        $response->assertSee("window.addEventListener('appinstalled', () => { deferredInstallPrompt = null; syncInstallAvailability(); })", false);
        $response->assertSee("window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true", false);
        $response->assertDontSee('data-install-app-trigger', false);
        $response->assertSee('data-mobile-install-app-trigger', false);
        $response->assertSee("@click=\"openDrawer('history')\"", false);
        $response->assertSee("@click=\"openDrawer('knockouts')\"", false);
        $response->assertSee('@mouseenter="open = true"', false);
        $response->assertSee('@mouseleave="open = false"', false);
        $response->assertSee('@click.stop', false);
        $response->assertSee('translate-x-full', false);
        $response->assertSee('height: calc(100dvh - ${headerHeight}px);', false);
        $response->assertSee('site-header fixed top-0 z-50 w-full bg-white shadow-lg transition-all duration-500 dark:bg-zinc-900', false);
        $response->assertDontSee(":class=\"{ 'dark:border-transparent': open }\"", false);
        $response->assertSee('dark:bg-zinc-900', false);
        $response->assertSee('text-sm font-semibold leading-6 transition', false);
        $response->assertSee('hover:text-green-700', false);
        $response->assertSee('dark:hover:text-green-500', false);
        $response->assertDontSee('dark:backdrop-blur', false);
        $response->assertSee('rounded-lg px-0 py-3 text-base font-semibold leading-7 text-gray-900', false);
        $response->assertSee('aria-label="Toggle main menu"', false);
        $response->assertSee('<span class="fa-stack -ml-1" aria-hidden="true">', false);
        $response->assertDontSee('<a href="#" class="-m-1.5 p-1.5">', false);
        $response->assertDontSee('<a href="/" class="fa-stack -ml-1">', false);
        $response->assertDontSee('id="searchIcon"', false);
        $response->assertDontSee('ring-opacity-5', false);
        $response->assertDontSee('sectionsOpen:', false);
        $response->assertDontSee('knockoutsOpen:', false);
        $response->assertDontSee('activeAccordion', false);
        $response->assertDontSee('data-mobile-menu-home', false);
        $response->assertDontSee('data-mobile-menu-close', false);
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
        $response->assertDontSee('href="'.route('knockout.index').'"', false);
    }

    public function test_home_page_lists_history_index_content_in_mobile_drawers(): void
    {
        $openSeason = Season::factory()->create(['is_open' => true]);
        $historySeason = Season::factory()->create([
            'is_open' => false,
            'name' => 'Winter 2025',
        ]);

        $ruleset = Ruleset::factory()->create([
            'name' => 'International Rules',
            'slug' => 'international-rules',
        ]);

        Section::factory()->create([
            'season_id' => $openSeason->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Current Section',
        ]);

        $historySection = Section::factory()->create([
            'season_id' => $historySeason->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Archived Section One',
        ]);

        $historyKnockout = Knockout::query()->create([
            'season_id' => $historySeason->id,
            'name' => 'Archived Singles Cup',
            'slug' => 'archived-singles-cup',
            'type' => KnockoutType::Singles->value,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('data-mobile-history-trigger', false);
        $response->assertSee('data-mobile-history-links', false);
        $response->assertSee('data-mobile-history-season-trigger', false);
        $response->assertSee('data-mobile-history-ruleset-trigger', false);
        $response->assertSee('data-mobile-history-section-links', false);
        $response->assertSee('data-mobile-history-knockout-link', false);
        $response->assertSee('data-mobile-menu-panel="history"', false);
        $response->assertSee('data-mobile-menu-panel="history-season-'.$historySeason->id.'"', false);
        $response->assertSee('Winter 2025', false);
        $response->assertSee('International Rules', false);
        $response->assertSee('Archived Section One', false);
        $response->assertSee('Archived Singles Cup', false);
        $response->assertSee('data-mobile-back-label', false);
        $response->assertSee(
            'href="'.route('history.section.show', ['season' => $historySeason, 'ruleset' => $ruleset, 'section' => $historySection]).'"',
            false
        );
        $response->assertSee('href="'.route('knockout.show', $historyKnockout).'"', false);
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

    public function test_authenticated_navigation_points_profile_links_to_account_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertSee('href="'.route('account.show').'"', false);
        $response->assertSeeText('Account');
        $response->assertSee('data-install-app-trigger', false);
        $response->assertSee('data-mobile-install-app-trigger', false);
        $response->assertDontSeeText('Your profile');
        $response->assertDontSeeText('Your team');
        $response->assertDontSee('href="'.route('player.show', $user).'"', false);
        $response->assertDontSee('href="'.route('support.tickets').'"', false);
    }

    public function test_frontend_footer_shows_stop_impersonating_link_when_impersonating(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $user = User::factory()->create();

        $this->actingAs($user);
        session([
            'impersonated_by' => $admin->id,
            'impersonator_guard' => 'web',
            'impersonator_guard_using' => 'web',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('href="'.route('impersonation.leave').'"', false);
        $response->assertSeeText('Stop impersonating');
    }

    public function test_frontend_header_menu_uses_app_impersonation_leave_route(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $user = User::factory()->create();

        $this->actingAs($user);
        session([
            'impersonated_by' => $admin->id,
            'impersonator_guard' => 'web',
            'impersonator_guard_using' => 'web',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('href="'.route('impersonation.leave').'"', false);
        $response->assertDontSee('href="'.route('filament-impersonate.leave').'"', false);
    }
}
