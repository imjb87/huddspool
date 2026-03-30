<header class="site-header fixed top-0 z-50 w-full bg-white shadow-lg transition-all duration-500 dark:bg-zinc-900"
    x-data="{
        open: false,
        activeDrawer: 'root',
        headerHeight: 0,
        headerHeightFrameId: null,
        headerResizeObserver: null,
        theme: 'light',
        deferredInstallPrompt: null,
        canInstallApp: false,
        updateHeaderHeight() {
            if (!this.$refs.header) {
                return;
            }

            this.headerHeight = Math.ceil(this.$refs.header.getBoundingClientRect().bottom);
        },
        scheduleHeaderHeightUpdate() {
            if (this.headerHeightFrameId) {
                window.cancelAnimationFrame(this.headerHeightFrameId);
            }

            this.headerHeightFrameId = window.requestAnimationFrame(() => {
                this.headerHeightFrameId = null;
                this.updateHeaderHeight();
            });
        },
        bindHeaderResizeObserver() {
            if (!this.$refs.header || typeof ResizeObserver === 'undefined') {
                return;
            }

            this.headerResizeObserver = new ResizeObserver(() => {
                this.scheduleHeaderHeightUpdate();
            });

            this.headerResizeObserver.observe(this.$refs.header);
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
            this.$nextTick(() => this.scheduleHeaderHeightUpdate());
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
    x-init="syncTheme(); syncInstallAvailability(); bindHeaderResizeObserver(); scheduleHeaderHeightUpdate(); $watch('open', value => document.body.classList.toggle('overflow-hidden', value)); window.addEventListener('resize', () => scheduleHeaderHeightUpdate()); window.addEventListener('site-theme-changed', () => syncTheme()); window.addEventListener('beforeinstallprompt', event => { event.preventDefault(); deferredInstallPrompt = event; syncInstallAvailability(); }); window.addEventListener('appinstalled', () => { deferredInstallPrompt = null; syncInstallAvailability(); })"
    x-ref="header">
    <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-5 lg:px-8" aria-label="Global">
        <div class="flex shrink-0">
            <a href="/" class="-m-1.5 p-1.5">
                <span class="sr-only">Huddersfield & District Tuesday Night Pool League</span>
                <x-application-logo />
            </a>
        </div>

        @include('layouts.partials.navigation-desktop-links')

        <div class="flex min-w-0 flex-1 items-center justify-end gap-x-4">
            <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-300"
                data-site-search-trigger aria-label="Open search">
                <span class="block w-5 flex items-center justify-center" aria-hidden="true">
                    <svg wire:loading.remove.delay.default="1" wire:target="search" class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"></path>
                    </svg>                
                </span>
            </button>
            <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-300"
                @click="toggleTheme()" aria-label="Toggle dark mode" data-theme-toggle>
                <span class="block w-5 flex items-center justify-center" aria-hidden="true">
                    <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon" x-cloak x-show="theme !== 'dark'" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 0 1 .26.77 7 7 0 0 0 9.958 7.967.75.75 0 0 1 1.067.853A8.5 8.5 0 1 1 6.647 1.921a.75.75 0 0 1 .808.083Z" clip-rule="evenodd"></path>
                    </svg>                    
                    <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon" x-cloak x-show="theme === 'dark'" aria-hidden="true">
                    <path d="M10 2a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 2ZM10 15a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 15ZM10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6ZM15.657 5.404a.75.75 0 1 0-1.06-1.06l-1.061 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM6.464 14.596a.75.75 0 1 0-1.06-1.06l-1.06 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM18 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 18 10ZM5 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 5 10ZM14.596 15.657a.75.75 0 0 0 1.06-1.06l-1.06-1.061a.75.75 0 1 0-1.06 1.06l1.06 1.06ZM5.404 6.464a.75.75 0 0 0 1.06-1.06l-1.06-1.06a.75.75 0 1 0-1.061 1.06l1.06 1.06Z"></path>
                    </svg>
                </span>
            </button>
            <button type="button" class="-m-2.5 inline-flex p-2.5 items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-300 lg:hidden"
                @click="open ? closeMenu() : openMenu()" :aria-expanded="open" aria-label="Toggle main menu"
                data-mobile-menu-toggle>
                <span class="sr-only">Toggle main menu</span>
                <span class="block w-6 flex items-center justify-center" aria-hidden="true">
                    <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon" x-cloak x-show="!open">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"></path>
                    </svg>
                    <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon" x-cloak x-show="open">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
                    </svg>                    
                </span>
            </button>
            @include('layouts.partials.navigation-desktop-account')            
        </div>

    </nav>
    @include('layouts.partials.navigation-mobile-drawer')
</header>
