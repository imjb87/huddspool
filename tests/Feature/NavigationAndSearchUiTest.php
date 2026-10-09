<?php

namespace Tests\Feature;

use App\KnockoutType;
use App\Models\Knockout;
use App\Models\Page;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\User;
use App\Notifications\LeagueResultSubmittedNotification;
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
        $response->assertSee('bg-black/20 transition-opacity dark:bg-black/60', false);
        $response->assertSee('overflow-hidden border-l border-gray-200 bg-white shadow-xl dark:border-neutral-800 dark:bg-neutral-950', false);
        $response->assertSee('data-site-search-trigger', false);
        $response->assertSee('rounded-lg bg-transparent', false);
        $response->assertSee('sm:rounded-[10px] sm:bg-gray-100', false);
        $response->assertSee('ml-2 hidden h-4 w-px shrink-0 bg-gray-200 lg:block dark:bg-neutral-800', false);
        $response->assertSee('role="separator" aria-orientation="vertical"', false);
        $response->assertDontSee('data-header-notifications-account-separator', false);
        $response->assertSee('aria-label="Open search"', false);
        $response->assertSee('class="size-4 sm:hidden"', false);
        $response->assertSee('class="hidden truncate sm:inline"', false);
        $response->assertSee('d="M3 10a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"', false);
        $response->assertSee('d="M21 21l-6 -6"', false);
        $response->assertSee('data-header-theme-toggle', false);
        $response->assertSee('text-gray-900 transition-colors duration-150 outline-none hover:bg-transparent hover:text-gray-900', false);
        $response->assertSee('aria-label="Toggle theme"', false);
        $response->assertSee('title="Toggle theme"', false);
        $response->assertSee('@click="toggleTheme()"', false);
        $response->assertSee('window.siteTheme?.toggleTheme?.()', false);
        $response->assertSee('aria-label="Log in"', false);
        $response->assertSee('data-header-login-link', false);
        $response->assertSee('href="'.route('login').'"', false);
        $response->assertSee('bg-black px-2.5 text-[0.8rem] font-medium whitespace-nowrap text-white', false);
        $response->assertDontSee('data-theme-toggle', false);
        $response->assertDontSee('data-mobile-theme-toggle', false);
        $response->assertDontSee('Open settings menu', false);
        $response->assertSee('fixed inset-0 z-10 flex items-start justify-center overflow-y-auto p-2 sm:items-center', false);
        $response->assertSee('site-theme', false);
        $response->assertSee('prefers-color-scheme: dark', false);
        $response->assertDontSee('<kbd class="ml-auto hidden', false);
        $response->assertDontSee('Ctrl K', false);
        $response->assertSee('placeholder="Search players, teams, venues..."', false);
        $response->assertSee('placeholder:text-gray-600 focus:ring-0 dark:text-gray-100 dark:placeholder:text-gray-400', false);
        $response->assertSee('data-search-modal-shell', false);
        $response->assertSee('max-w-none overflow-hidden rounded-xl', false);
        $response->assertSee('rounded-xl border border-gray-200/80 bg-white p-2 pb-11', false);
        $response->assertSee('endpoint:', false);
        $response->assertSee('h-9 min-w-0 flex-1 border-0 bg-transparent px-0 text-sm', false);
        $response->assertSee('data-search-loading-skeleton', false);
        $response->assertSee('aria-hidden="true"', false);
        $response->assertSee('animate-pulse rounded-md bg-gray-200/80', false);
        $response->assertSee('min-h-80 max-h-[28rem] overflow-y-auto scroll-py-1.5', false);
        $response->assertSee('flex h-9 w-full items-center justify-between gap-4 rounded-md border border-transparent', false);
        $response->assertSee('data-search-player-avatar', false);
        $response->assertSee('Navigate', false);
        $response->assertSee('Open', false);
        $response->assertSee('Close', false);
        $response->assertSee('h-16 w-full items-center gap-2', false);
        $response->assertSee('class="relative hidden lg:ml-4 lg:flex lg:items-center lg:gap-1"', false);
        $response->assertSee('ml-auto flex min-w-0 flex-1 items-center justify-end gap-2 sm:flex-none', false);
        $response->assertSee('class="size-4.5"', false);
        $response->assertSee('class="size-4"', false);
        $response->assertSee('d="M4 6l16 0"', false);
        $response->assertSee('d="M4 12l16 0"', false);
        $response->assertSee('d="M4 18l16 0"', false);
        $response->assertSee('data-mobile-menu-icon', false);
        $response->assertSee('data-mobile-menu-icon-state="closed"', false);
        $response->assertSee('data-mobile-menu-icon-top', false);
        $response->assertSee('data-mobile-menu-icon-middle', false);
        $response->assertSee('data-mobile-menu-icon-bottom', false);
        $response->assertSee('<a href="/"', false);
        $response->assertSeeText('International Rules');
        $response->assertSeeText('Blackball Rules');
        $response->assertSeeText('EPA Rules');
        $response->assertSeeText('History');
        $response->assertSeeText('Knockouts');
        $response->assertSeeText('Official');
        $response->assertSeeInOrder(['Knockouts', 'Official', 'History']);
        $response->assertDontSee('href="'.route('news.index').'"', false);
        $response->assertSeeText('Handbook');
        $response->assertSee('href="'.route('downloads.index').'"', false);
        $response->assertDontSee('data-user-menu-link="downloads"', false);
        $response->assertDontSeeText('Ruleset');
        $response->assertSee('href="'.route('ruleset.rules', $firstRuleset).'"', false);
        $response->assertSee('<a href="/"', false);
        $response->assertSee('data-mobile-menu-toggle', false);
        $response->assertSee('data-mobile-menu-drawer', false);
        $response->assertSee('x-transition:enter-start="ui-motion-drawer-enter-start"', false);
        $response->assertSee('x-transition:leave-end="ui-motion-drawer-leave-end"', false);
        $response->assertSee('data-mobile-menu-panel="root"', false);
        $response->assertDontSeeText('Appearance');
        $response->assertSee('data-mobile-ruleset-trigger', false);
        $response->assertSee('data-mobile-ruleset-sections', false);
        $response->assertSee('data-knockouts-nav', false);
        $response->assertSee('data-mobile-history-trigger', false);
        $response->assertSee('data-mobile-history-links', false);
        $response->assertSee('data-mobile-knockouts-trigger', false);
        $response->assertSee('data-mobile-knockouts-links', false);
        $response->assertSee('data-mobile-official-trigger', false);
        $response->assertSee('data-mobile-official-links', false);
        $response->assertSee('data-mobile-menu-panel="official"', false);
        $response->assertSee('data-mobile-back-label', false);
        $response->assertSee("activeDrawer: 'root'", false);
        $response->assertSee("navigationDirection: 'forward'", false);
        $response->assertSee('mobileMenuPanelClasses(panel)', false);
        $response->assertSee('ui-motion-panel-in', false);
        $response->assertSee('syncMobileMenuIcon()', false);
        $response->assertSee("open && activeDrawer === 'root' ? closeMenu() : openMenu('root')", false);
        $response->assertSee("\$watch('open', value => document.body.classList.toggle('overflow-hidden', value))", false);
        $response->assertDontSee('data-header-notifications-trigger', false);
        $response->assertDontSee('data-notifications-drawer', false);
        $response->assertSee('deferredInstallPrompt: null', false);
        $response->assertSee('canInstallApp: false', false);
        $response->assertSee("window.addEventListener('beforeinstallprompt', event => { event.preventDefault(); deferredInstallPrompt = event; syncInstallAvailability(); });", false);
        $response->assertSee("window.addEventListener('appinstalled', () => { deferredInstallPrompt = null; syncInstallAvailability(); })", false);
        $response->assertSee("window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true", false);
        $response->assertDontSee('data-install-app-trigger', false);
        $response->assertSee('data-mobile-install-app-trigger', false);
        $response->assertSee("@click=\"openDrawer('history')\"", false);
        $response->assertSee("@click=\"openDrawer('knockouts')\"", false);
        $response->assertSee("window.matchMedia('(hover: none), (pointer: coarse)').matches", false);
        $response->assertSee('@mouseenter="openOnHover()"', false);
        $response->assertSee('@mouseleave="closeOnHover()"', false);
        $response->assertSee('data-navigation-menu', false);
        $response->assertSee('data-navigation-menu-trigger', false);
        $response->assertSee('data-navigation-menu-content', false);
        $response->assertSee('@focusin="show()"', false);
        $response->assertSee('@keydown.escape.stop="close(); $el.querySelector(\'[data-navigation-menu-trigger]\')?.focus()"', false);
        $response->assertSee('@click.outside="close()"', false);
        $response->assertSee('@keydown.arrowright.prevent="focusTrigger(1)"', false);
        $response->assertSee('@keydown.arrowleft.prevent="focusTrigger(-1)"', false);
        $response->assertSee('@keydown.home.prevent="focusTrigger(\'first\')"', false);
        $response->assertSee('@keydown.end.prevent="focusTrigger(\'last\')"', false);
        $response->assertSee('data-[state=open]:bg-gray-100', false);
        $response->assertSee('h-8 w-max items-center justify-center gap-1.5 rounded-md bg-white px-2.5 py-2', false);
        $response->assertSee('x-transition:enter-start="translate-y-1 scale-95 opacity-0"', false);
        $response->assertSee('mt-1.5 w-72 origin-top-left', false);
        $response->assertDontSee('size-4 shrink-0 items-center justify-center text-gray-500 dark:text-gray-400', false);
        $response->assertSee('@click="toggle()"', false);
        $response->assertSee("\$dispatch('nav-dropdown-open', { id: this.id })", false);
        $response->assertDontSee('Open notifications menu', false);
        $response->assertSee('@click.stop', false);
        $response->assertSee('ui-motion-drawer-enter-start', false);
        $response->assertSee('height: calc(100dvh - ${headerHeight}px);', false);
        $response->assertSee('site-header fixed top-0 z-50 w-full bg-white dark:bg-neutral-950', false);
        $response->assertSee('ui-page-shell', false);
        $response->assertSee('data-mobile-menu-drawer', false);
        $response->assertSee('overflow-hidden border-l border-gray-200 bg-white shadow-xl dark:border-neutral-800 dark:bg-neutral-950', false);
        $response->assertSee('navigation-mobile-menu relative h-full overflow-hidden bg-white dark:bg-neutral-950', false);
        $response->assertSee('bg-black/20 transition-opacity dark:bg-black/60', false);
        $response->assertDontSee(":class=\"{ 'dark:border-transparent': open }\"", false);
        $response->assertSee('dark:bg-neutral-900', false);
        $response->assertSee('text-sm leading-5 font-medium whitespace-nowrap text-gray-900 transition-[color,box-shadow]', false);
        $response->assertSee('hover:bg-gray-100', false);
        $response->assertSee('dark:hover:bg-neutral-800', false);
        $response->assertDontSee('dark:backdrop-blur', false);
        $response->assertSee('aria-label="Toggle main menu"', false);
        $response->assertSee('rounded-md text-gray-900 outline-none transition-colors hover:bg-transparent hover:text-gray-900', false);
        $response->assertDontSee('<a href="#" class="-m-1.5 p-1.5">', false);
        $response->assertDontSee('<a href="/" class="fa-stack -ml-1">', false);
        $response->assertDontSee('id="searchIcon"', false);
        $response->assertDontSee('ring-opacity-5', false);
        $response->assertDontSee('sectionsOpen:', false);
        $response->assertDontSee('knockoutsOpen:', false);
        $response->assertDontSee('activeAccordion', false);
        $response->assertDontSee('data-mobile-menu-home', false);
        $response->assertDontSee('data-mobile-menu-close', false);
        $response->assertDontSee('href="/rulesets"', false);
        $response->assertDontSee('aria-label="Primary mobile"', false);
        $response->assertSee('navigation-mobile-menu relative h-full overflow-hidden bg-white dark:bg-neutral-950', false);
        $response->assertSee('ui-card', false);
        $response->assertSee('ui-card-rows', false);
        $response->assertSee('ui-card-row-link', false);
        $response->assertSee('ui-card-row w-full cursor-pointer items-center justify-between', false);
        $response->assertSee('text-sm leading-5 font-medium', false);
        $response->assertDontSee('inline-flex size-8 items-center justify-center rounded-full ring-1', false);
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
        $response->assertSee('data-history-navigation', false);
        $response->assertSee('data-history-navigation-content', false);
        $response->assertSee('data-history-navigation-season', false);
        $response->assertSee('data-history-navigation-season-trigger', false);
        $response->assertSee('data-history-navigation-season-panel', false);
        $response->assertSee('activeHistorySeason', false);
        $response->assertSee('seasonKey', false);
        $response->assertSee('data-history-navigation-grid', false);
        $response->assertSee('data-history-navigation-items', false);
        $response->assertSee('data-history-navigation-seasons-grid', false);
        $response->assertSee('data-history-navigation-item', false);
        $response->assertSee('data-history-navigation-section-link', false);
        $response->assertSee('data-history-navigation-knockout-link', false);
        $response->assertDontSee('Historical standings and results.', false);
        $response->assertDontSee('Historical knockout bracket.', false);
        $response->assertDontSee('data-history-archive-link', false);
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
        $response->assertSee('href="'.route('history.knockout.show', [
            'season' => $historySeason,
            'knockout' => $historyKnockout,
        ]).'"', false);
    }

    public function test_authenticated_home_page_renders_header_notifications_controls(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create(['name' => 'International Rules']);
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Premier Division',
        ]);

        $user = User::factory()->create();
        $result = Result::factory()->create([
            'section_id' => $section->id,
        ]);

        $user->notify(new LeagueResultSubmittedNotification($result));

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertSee('data-header-notifications-trigger', false);
        $response->assertSee('aria-label="Open notifications"', false);
        $response->assertSee('data-notifications-drawer', false);
        $response->assertSee('data-notifications-links', false);
        $response->assertSee('data-unread-notifications-badge', false);
        $response->assertSee('data-header-notifications-account-separator', false);
        $response->assertSeeText('Notifications');
        $response->assertSeeText('Mark all as read');
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
        $result = Result::factory()->create();

        $user->notify(new LeagueResultSubmittedNotification($result));

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertSee('href="'.route('account.show').'"', false);
        $response->assertSeeText($user->name);
        $response->assertSee('alt="'.$user->name.' avatar"', false);
        $response->assertSee('src="'.$user->avatar_url.'"', false);
        $response->assertSee('bg-black px-2.5 text-[0.8rem] font-medium whitespace-nowrap text-white', false);
        $response->assertSee('class="size-5 rounded-full object-cover ring-1 ring-white/20 dark:ring-black/10"', false);
        $response->assertSee('right-0 top-full z-50 mt-1.5 w-72 origin-top-right', false);
        $response->assertSee('data-header-theme-toggle', false);
        $response->assertDontSee('data-theme-toggle', false);
        $response->assertSee('data-install-app-trigger', false);
        $response->assertSee('data-mobile-install-app-trigger', false);
        $response->assertDontSeeText('Your profile');
        $response->assertDontSeeText('Your team');
        $response->assertDontSee('href="'.route('player.show', $user).'"', false);
        $response->assertDontSee('href="'.route('support.tickets').'"', false);
        $response->assertSeeText('Open user menu for '.$user->name);
        $response->assertSee('data-header-notifications-trigger', false);
        $response->assertSee('data-notifications-drawer', false);
        $response->assertSee('data-header-notifications-account-separator', false);
        $response->assertSeeText('Notifications');
        $response->assertSee('data-notifications-mark-all', false);
        $response->assertSeeText('Install app');
        $response->assertSee('href="'.route('downloads.index').'"', false);
        $response->assertDontSee('data-user-menu-link="downloads"', false);
        $response->assertSeeText('Log out');
        $response->assertDontSee('href="'.route('filament.admin.pages.dashboard').'"', false);
        $response->assertDontSeeText('Log in');
    }

    public function test_admin_users_see_admin_link_in_authenticated_navigation(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertSee('href="'.route('filament.admin.pages.dashboard').'"', false);
        $response->assertSeeText('Admin');
        $response->assertSee('Open user menu for '.$user->name, false);
        $response->assertSeeText('Log out');
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
