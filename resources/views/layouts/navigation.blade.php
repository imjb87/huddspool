<header class="site-header fixed top-0 z-50 w-full bg-white/85 shadow-sm ring-1 ring-neutral-950/5 backdrop-blur-md transition-all duration-500 dark:bg-neutral-900/85 dark:ring-neutral-100/10"
    x-data="{
        open: false,
        notificationsOpen: false,
        activeDrawer: 'root',
        headerHeight: 0,
        headerHeightFrameId: null,
        headerResizeObserver: null,
        themePreference: 'system',
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
            this.themePreference = window.siteTheme?.currentPreference?.() ?? 'system';
        },
        syncInstallAvailability() {
            const standalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
            this.canInstallApp = !!this.deferredInstallPrompt && !standalone;
        },
        setTheme(theme) {
            this.themePreference = window.siteTheme?.setTheme?.(theme) ?? theme;
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
            this.notificationsOpen = false;
            this.open = true;
            this.activeDrawer = drawer;
            this.$nextTick(() => this.scheduleHeaderHeightUpdate());
        },
        closeMenu() {
            this.open = false;
            this.activeDrawer = 'root';
        },
        openNotificationsDrawer() {
            this.open = false;
            this.activeDrawer = 'root';
            this.notificationsOpen = true;
            this.$store.headerNotifications.refresh();
            this.$nextTick(() => this.scheduleHeaderHeightUpdate());
        },
        closeNotificationsDrawer() {
            this.notificationsOpen = false;
        },
        openDrawer(drawer) {
            this.activeDrawer = drawer;
        },
        goBackToRoot() {
            this.activeDrawer = 'root';
        },
    }"
    x-init="syncTheme(); syncInstallAvailability(); bindHeaderResizeObserver(); scheduleHeaderHeightUpdate(); $watch('open', value => document.body.classList.toggle('overflow-hidden', value || notificationsOpen)); $watch('notificationsOpen', value => document.body.classList.toggle('overflow-hidden', open || value)); window.addEventListener('resize', () => scheduleHeaderHeightUpdate()); window.addEventListener('site-theme-changed', () => syncTheme()); window.addEventListener('beforeinstallprompt', event => { event.preventDefault(); deferredInstallPrompt = event; syncInstallAvailability(); }); window.addEventListener('appinstalled', () => { deferredInstallPrompt = null; syncInstallAvailability(); })"
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
            <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-neutral-700 dark:text-neutral-300"
                data-site-search-trigger aria-label="Open search">
                <span class="block w-5 flex items-center justify-center" aria-hidden="true">
                    <svg wire:loading.remove.delay.default="1" wire:target="search" class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"></path>
                    </svg>                
                </span>
            </button>
            @auth
                <button type="button"
                    class="relative -m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-neutral-700 dark:text-neutral-300 lg:hidden"
                    :class="notificationsOpen ? 'rounded-full bg-neutral-100 dark:bg-neutral-800' : ''"
                    @click="notificationsOpen ? closeNotificationsDrawer() : openNotificationsDrawer()"
                    :aria-expanded="notificationsOpen"
                    aria-label="Open notifications drawer"
                    data-mobile-notifications-toggle
                    x-init="$store.headerNotifications.configure({
                        summaryUrl: @js(route('account.notifications.summary')),
                        readAllUrl: @js(route('account.notifications.read-all')),
                        readUrlTemplate: @js(route('account.notifications.read', ['notification' => '__NOTIFICATION__'])),
                    })">
                    <span class="sr-only">Open notifications drawer</span>
                    <span class="block w-5 flex items-center justify-center" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-neutral-700 dark:text-neutral-300" :class="notificationsOpen ? '!text-green-600 dark:!text-green-500' : ''">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75v-.7V9a6 6 0 0 0-12 0v.05c0 .237 0 .476-.003.713A8.967 8.967 0 0 1 3.69 15.772a23.852 23.852 0 0 0 5.454 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </span>
                    <template x-if="$store.headerNotifications.unreadCount > 0">
                        <span
                            class="absolute right-2 top-2 inline-flex size-2.5 rounded-full bg-green-600 ring-2 ring-white dark:ring-neutral-950"
                            data-mobile-unread-notifications-badge
                            aria-hidden="true"></span>
                    </template>
                </button>
            @endauth
            <button type="button" class="-m-2.5 inline-flex p-2.5 items-center justify-center rounded-md p-2.5 text-neutral-700 dark:text-neutral-300 lg:hidden"
                @click="open && activeDrawer === 'root' ? closeMenu() : openMenu('root')" :aria-expanded="open && activeDrawer === 'root'" aria-label="Toggle main menu"
                data-mobile-menu-toggle>
                <span class="sr-only">Toggle main menu</span>
                <span class="block w-6 flex items-center justify-center" aria-hidden="true">
                    <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon" x-cloak x-show="!open || activeDrawer !== 'root'">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"></path>
                    </svg>
                    <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon" x-cloak x-show="open && activeDrawer === 'root'">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
                    </svg>                    
                </span>
            </button>
            @include('layouts.partials.navigation-desktop-account')            
        </div>

    </nav>
    @include('layouts.partials.navigation-mobile-drawer')
    @include('layouts.partials.navigation-mobile-notifications-drawer')
</header>
