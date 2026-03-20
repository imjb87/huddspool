@php(extract($navigation_view_data ?? [], EXTR_SKIP))
@php($navigationRulesets = collect($navigation_rulesets ?? []))
@php($historySeasonGroups = collect($navigation_history_season_groups ?? []))
@php($navigableKnockouts = collect($navigation_active_knockouts ?? []))

<header class="site-header fixed top-0 z-50 w-full bg-white shadow-lg transition-all duration-500 dark:border-b dark:border-zinc-800 dark:bg-zinc-900"
    x-data="{
        open: false,
        activeDrawer: 'root',
        headerHeight: 0,
        theme: 'light',
        deferredInstallPrompt: null,
        canInstallApp: false,
        updateHeaderHeight() {
            this.headerHeight = Math.ceil(this.$refs.header.getBoundingClientRect().bottom);
        },
        syncTheme() {
            this.theme = window.siteTheme?.currentTheme?.() ?? 'light';
        },
        syncInstallAvailability() {
            const standalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
            this.canInstallApp = !!this.deferredInstallPrompt && !standalone;
        },
        toggleTheme() {
            this.theme = window.siteTheme?.toggleTheme?.() ?? (this.theme === 'dark' ? 'light' : 'dark');
        },
        async installApp() {
            if (!this.deferredInstallPrompt) {
                return;
            }

            this.deferredInstallPrompt.prompt();
            await this.deferredInstallPrompt.userChoice;
            this.deferredInstallPrompt = null;
            this.syncInstallAvailability();
        },
        openMenu() {
            this.open = true;
            this.activeDrawer = 'root';
            this.$nextTick(() => this.updateHeaderHeight());
        },
        closeMenu() {
            this.open = false;
            this.activeDrawer = 'root';
        },
        openDrawer(drawer) {
            this.activeDrawer = drawer;
        },
        goBackToRoot() {
            this.activeDrawer = 'root';
        },
    }"
    x-init="syncTheme(); syncInstallAvailability(); updateHeaderHeight(); $watch('open', value => document.body.classList.toggle('overflow-hidden', value)); window.addEventListener('resize', () => updateHeaderHeight()); window.addEventListener('site-theme-changed', () => syncTheme()); window.addEventListener('beforeinstallprompt', event => { event.preventDefault(); deferredInstallPrompt = event; syncInstallAvailability(); }); window.addEventListener('appinstalled', () => { deferredInstallPrompt = null; syncInstallAvailability(); })"
    x-ref="header">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-5 lg:px-8" aria-label="Global">
        <div class="flex flex-1">
            <a href="/" class="-m-1.5 p-1.5">
                <span class="sr-only">Huddersfield & District Tuesday Night Pool League</span>
                <x-application-logo />
            </a>
        </div>

        @include('layouts.partials.navigation-desktop-links')

        <div class="flex items-center gap-x-4 lg:hidden">
            <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-300"
                data-site-search-trigger aria-label="Open search">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-300"
                @click="toggleTheme()" aria-label="Toggle dark mode" data-theme-toggle>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" x-cloak x-show="theme !== 'dark'">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                </svg>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" x-cloak x-show="theme === 'dark'">
                    <path fill-rule="evenodd" d="M10 2.75a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0V3.5a.75.75 0 01.75-.75zM10 14a4 4 0 100-8 4 4 0 000 8zm0 3.25a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0V18a.75.75 0 01.75-.75zM4.166 4.166a.75.75 0 011.06 0l1.06 1.061a.75.75 0 11-1.06 1.06L4.166 5.23a.75.75 0 010-1.06zm9.548 9.548a.75.75 0 011.06 0l1.06 1.06a.75.75 0 01-1.06 1.061l-1.06-1.06a.75.75 0 010-1.061zM2.75 10a.75.75 0 01.75-.75H5a.75.75 0 010 1.5H3.5A.75.75 0 012.75 10zm12.25 0a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5H15.75A.75.75 0 0115 10zM5.227 13.713a.75.75 0 011.06 1.061l-1.06 1.06a.75.75 0 11-1.061-1.06l1.061-1.06zm10.607-9.547a.75.75 0 010 1.06l-1.06 1.061a.75.75 0 11-1.061-1.06l1.06-1.061a.75.75 0 011.061 0z" clip-rule="evenodd" />
                </svg>
            </button>
            <button type="button" class="-m-2.5 inline-flex p-2.5 items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-300"
                @click="open ? closeMenu() : openMenu()" :aria-expanded="open" aria-label="Toggle main menu"
                data-mobile-menu-toggle>
                <span class="sr-only">Toggle main menu</span>
                <span class="block h-6 w-6 flex items-center justify-center" aria-hidden="true">
                    <span class="relative block h-[18px] w-[18px]">
                        <span class="absolute left-0 top-[3px] block h-[1.5px] w-[18px] rounded-full bg-current transition-all duration-200"
                            :class="open ? '!top-[9px] -rotate-135' : ''"></span>
                        <span class="absolute left-0 top-[8px] block h-[1.5px] w-[18px] rounded-full bg-current transition-all duration-200"
                            :class="open ? 'opacity-0' : 'opacity-100'"></span>
                        <span class="absolute left-0 top-[13px] block h-[1.5px] w-[18px] rounded-full bg-current transition-all duration-200"
                            :class="open ? '!top-[9px] rotate-135' : ''"></span>
                    </span>
                </span>
            </button>
        </div>

        @include('layouts.partials.navigation-desktop-account')
    </nav>

    @include('layouts.partials.navigation-mobile-drawer')
    @include('layouts.partials.navigation-search-script')
</header>
