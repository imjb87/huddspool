<header class="site-header fixed top-0 z-50 w-full bg-white dark:bg-neutral-950"
    x-data="{
        open: false,
        activeDrawer: 'root',
        navigationDirection: 'forward',
        headerHeight: 0,
        headerHeightFrameId: null,
        headerResizeObserver: null,
        deferredInstallPrompt: null,
        canInstallApp: false,
        updateHeaderHeight() {
            if (!this.$refs.header) {
                return;
            }

            this.headerHeight = Math.ceil(this.$refs.header.getBoundingClientRect().bottom);
            document.documentElement.style.setProperty('--site-header-height', `${this.headerHeight}px`);
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
        syncInstallAvailability() {
            const standalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
            this.canInstallApp = !!this.deferredInstallPrompt && !standalone;
        },
        toggleTheme() {
            window.siteTheme?.toggleTheme?.();
        },
        syncMobileMenuIcon() {
            window.mobileMenuIcon?.set(this.$refs.mobileMenuIcon, this.open);
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
        openMenu(drawer = 'root') {
            this.open = true;
            this.navigationDirection = 'forward';
            this.activeDrawer = drawer;
            this.syncMobileMenuIcon();
            window.dispatchEvent(new CustomEvent('header-overlay-open', { detail: { id: 'mobile-menu' } }));
            this.$nextTick(() => this.scheduleHeaderHeightUpdate());
        },
        closeMenu() {
            this.open = false;
            this.activeDrawer = 'root';
            this.navigationDirection = 'forward';
            this.syncMobileMenuIcon();
        },
        openDrawer(drawer, direction = 'forward') {
            this.navigationDirection = direction;
            this.activeDrawer = drawer;
            this.syncMobileMenuIcon();
        },
        goBackToRoot() {
            this.openDrawer('root', 'back');
        },
        mobileMenuPanelClasses(panel) {
            return {
                'mobile-menu-panel--active': this.activeDrawer === panel,
                'mobile-menu-panel--inactive': this.activeDrawer !== panel,
                'mobile-menu-panel--forward': this.navigationDirection === 'forward',
                'mobile-menu-panel--back': this.navigationDirection === 'back',
            };
        },
    }"
    x-init="syncInstallAvailability(); bindHeaderResizeObserver(); scheduleHeaderHeightUpdate(); syncMobileMenuIcon(); $watch('open', value => document.body.classList.toggle('overflow-hidden', value)); window.addEventListener('resize', () => scheduleHeaderHeightUpdate()); window.addEventListener('beforeinstallprompt', event => { event.preventDefault(); deferredInstallPrompt = event; syncInstallAvailability(); }); window.addEventListener('appinstalled', () => { deferredInstallPrompt = null; syncInstallAvailability(); })"
    @header-overlay-open.window="if ($event.detail.id !== 'mobile-menu' && open) closeMenu()"
    x-ref="header">
    <nav class="flex h-16 w-full items-center gap-2 px-4 sm:px-6" aria-label="Global">
        <div class="flex shrink-0">
            <a href="/"
                class="inline-flex -mt-1 h-8 w-10 items-center justify-center rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-green-600/50">
                <span class="sr-only">Huddersfield & District Tuesday Night Pool League</span>
                <x-application-logo />
            </a>
        </div>

        @include('layouts.partials.navigation-desktop-links')

        <div class="ml-auto flex min-w-0 flex-1 items-center justify-end gap-2 sm:flex-none">
            <button type="button"
                class="inline-flex size-8 shrink-0 items-center justify-center rounded-lg bg-transparent text-sm font-medium whitespace-nowrap text-black shadow-none outline-none transition-colors hover:bg-gray-100 hover:text-black focus-visible:ring-2 focus-visible:ring-gray-900/20 sm:h-8 sm:w-28 sm:min-w-0 sm:flex-none sm:justify-start sm:gap-2 sm:rounded-[10px] sm:bg-gray-100 sm:px-4 sm:py-2 sm:pl-3 sm:hover:bg-gray-200/70 md:w-48 lg:w-64 dark:bg-transparent dark:text-gray-50 dark:hover:bg-neutral-800/50 dark:hover:text-gray-50 dark:focus-visible:ring-gray-100/20 sm:dark:bg-neutral-900"
                @click="window.headerActionIcon?.set($el.querySelector('[data-header-action-icon=search]'), $el.getAttribute('aria-expanded') !== 'true'); window.dispatchEvent(new CustomEvent('site-search:toggle'))"
                data-site-search-trigger aria-label="Open search" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true" data-header-action-icon="search" data-header-action-icon-state="closed">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <g data-header-action-icon-group>
                        <path d="M3 10a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" data-header-action-icon-primary />
                        <path d="M21 21l-6 -6" data-header-action-icon-secondary />
                    </g>
                </svg>
                <span class="hidden truncate sm:inline" data-search-trigger-label>Search...</span>
            </button>
            <div class="ml-2 hidden h-4 w-px shrink-0 bg-gray-200 lg:block dark:bg-neutral-800" role="separator" aria-orientation="vertical"></div>
            <button type="button"
                class="group/toggle inline-flex size-8 shrink-0 items-center justify-center gap-2 rounded-lg text-sm font-medium whitespace-nowrap text-gray-900 transition-colors duration-150 outline-none hover:bg-transparent hover:text-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900/20 dark:text-gray-100 dark:hover:bg-transparent dark:hover:text-gray-100 dark:focus-visible:ring-gray-100/20"
                @click="toggleTheme()"
                aria-label="Toggle theme"
                title="Toggle theme"
                data-header-theme-toggle>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                    <path d="M12 3l0 18" />
                    <path d="M12 9l4.65 -4.65" />
                    <path d="M12 14.3l7.37 -7.37" />
                    <path d="M12 19.6l8.85 -8.85" />
                </svg>
                <span class="sr-only">Toggle theme</span>
            </button>
            @include('components.account.notifications-drawer')
            @auth
                <div class="ml-2 hidden h-4 w-px shrink-0 bg-gray-200 lg:block dark:bg-neutral-800"
                    role="separator"
                    aria-orientation="vertical"
                    data-header-notifications-account-separator></div>
            @endauth
            <button type="button" class="inline-flex size-8 items-center justify-center rounded-md text-gray-900 outline-none transition-colors hover:bg-transparent hover:text-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900/20 lg:hidden dark:text-gray-100 dark:hover:bg-transparent dark:hover:text-gray-100 dark:focus-visible:ring-gray-100/20"
                @click="open ? closeMenu() : openMenu('root')" :aria-expanded="open" aria-label="Toggle main menu"
                data-mobile-menu-toggle>
                <span class="sr-only">Toggle main menu</span>
                <span class="relative flex size-5 items-center justify-center" aria-hidden="true">
                    <svg class="absolute size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-slot="icon" data-mobile-menu-icon data-mobile-menu-icon-state="closed" x-ref="mobileMenuIcon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <g data-mobile-menu-icon-group>
                            <path d="M4 6l16 0" data-mobile-menu-icon-top />
                            <path d="M4 12l16 0" data-mobile-menu-icon-middle />
                            <path d="M4 18l16 0" data-mobile-menu-icon-bottom />
                        </g>
                    </svg>
                </span>
            </button>
            @include('layouts.partials.navigation-desktop-account')            
        </div>

    </nav>
    @include('layouts.partials.navigation-mobile-drawer')
</header>
