<header class="site-header fixed top-0 z-50 w-full bg-white shadow-lg transition-all duration-500 dark:bg-zinc-900"
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
        <div class="flex shrink-0">
            <a href="/" class="-m-1.5 p-1.5">
                <span class="sr-only">Huddersfield & District Tuesday Night Pool League</span>
                <x-application-logo />
            </a>
        </div>

        @include('layouts.partials.navigation-desktop-links')

        <div class="flex items-center gap-x-4 lg:hidden">
            <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-300"
                data-site-search-trigger aria-label="Open search">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </button>
            <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-300"
                @click="toggleTheme()" aria-label="Toggle dark mode" data-theme-toggle>
                <span class="block w-6 flex items-center justify-center" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5" x-cloak x-show="theme !== 'dark'" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                    <svg class="size-6" fill="none" aria-hidden="true" x-cloak x-show="theme === 'dark'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                </span>
            </button>
            <button type="button" class="-m-2.5 inline-flex p-2.5 items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-300"
                @click="open ? closeMenu() : openMenu()" :aria-expanded="open" aria-label="Toggle main menu"
                data-mobile-menu-toggle>
                <span class="sr-only">Toggle main menu</span>
                <span class="block h-6 w-6 flex items-center justify-center" aria-hidden="true">
                    <span class="relative block h-[20px] w-[20px]">
                        <span class="absolute left-0 top-[4px] block h-[1.5px] w-[20px] rounded-full bg-current transition-all duration-200"
                            :class="open ? '!top-[10px] -rotate-135' : ''"></span>
                        <span class="absolute left-0 top-[10px] block h-[1.5px] w-[20px] rounded-full bg-current transition-all duration-200"
                            :class="open ? 'opacity-0' : 'opacity-100'"></span>
                        <span class="absolute left-0 top-[16px] block h-[1.5px] w-[20px] rounded-full bg-current transition-all duration-200"
                            :class="open ? '!top-[10px] rotate-135' : ''"></span>
                    </span>
                </span>
            </button>
        </div>

        @include('layouts.partials.navigation-desktop-account')
    </nav>

    @include('layouts.partials.navigation-mobile-drawer')
    @include('layouts.partials.navigation-search-script')
</header>
