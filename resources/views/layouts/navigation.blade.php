@php
    $currentRuleset = request()->route('ruleset');
    $currentPage = request()->route('page');
    $isRulesetRoute = request()->routeIs('ruleset.show', 'ruleset.section.show', 'table.index', 'fixture.index', 'player.index');
    $historyRulesetOrder = [
        'international-rules' => 0,
        'blackball-rules' => 1,
        'epa-rules' => 2,
    ];
    $historySeasonGroups = collect($past_seasons ?? [])
        ->filter(fn ($season) => $season?->hasConcluded())
        ->values()
        ->map(function ($season) use ($historyRulesetOrder) {
            $rulesets = $season->sections
                ->filter(fn ($section) => $section->ruleset && filled($section->slug))
                ->groupBy('ruleset_id')
                ->map(function ($sections) use ($historyRulesetOrder) {
                    $firstSection = $sections->first();

                    return [
                        'ruleset' => $firstSection->ruleset,
                        'sort_order' => $historyRulesetOrder[$firstSection->ruleset->slug ?? ''] ?? PHP_INT_MAX,
                        'sections' => $sections
                            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                            ->values(),
                    ];
                })
                ->sortBy(fn (array $group) => sprintf('%03d-%s', $group['sort_order'], $group['ruleset']->name))
                ->values();

            return [
                'season' => $season,
                'rulesets' => $rulesets,
            ];
        });
    $navigableKnockouts = collect($active_knockouts ?? [])
        ->filter(fn ($knockout) => filled($knockout?->slug))
        ->values();
    $isKnockoutRoute = request()->routeIs('knockout.*')
        || (request()->routeIs('page.show') && $currentPage === 'knockout-dates');
@endphp

<header class="site-header fixed top-0 z-50 w-full bg-white shadow-lg transition-all duration-500 dark:border-b dark:border-zinc-800/80 dark:bg-zinc-900"
    :class="{ 'dark:border-transparent': open }"
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

        <div class="hidden lg:flex lg:items-center lg:gap-x-6">
            @foreach ($rulesets as $ruleset)
                @php
                    $navigableSections = $ruleset->openSections
                        ->filter(fn ($section) => filled($section?->getRouteKey()))
                        ->values();
                    $isActiveRuleset = $isRulesetRoute && $currentRuleset instanceof \App\Models\Ruleset && $currentRuleset->is($ruleset);
                @endphp
                @continue($navigableSections->isEmpty())
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button type="button"
                        class="flex items-center gap-x-1 text-sm font-semibold leading-6 {{ $isActiveRuleset ? 'text-green-700 dark:text-green-500' : 'text-gray-900 hover:text-green-700 dark:text-gray-100 dark:hover:text-green-500' }}"
                        @click="open = ! open" :aria-expanded="open">
                        {{ $ruleset->name }}
                        <svg class="h-4 w-4 flex-none text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div class="absolute left-0 top-full z-10 mt-3 w-72 rounded-2xl bg-white p-2 shadow-lg ring-1 ring-gray-900/5 dark:bg-zinc-900 dark:ring-white/10"
                        x-show="open"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1">
                        @foreach ($navigableSections as $section)
                            <a href="{{ route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section]) }}"
                                class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800">
                                {{ $section->name }}
                            </a>
                        @endforeach
                        <div class="mx-2 my-1 border-t border-gray-200 dark:border-gray-800"></div>
                        <a href="{{ route('ruleset.show', $ruleset) }}"
                            class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800">
                            {{ $ruleset->name }}
                        </a>
                    </div>
                </div>
            @endforeach

            <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <button type="button"
                    class="flex items-center gap-x-1 text-sm font-semibold leading-6 {{ $isKnockoutRoute ? 'text-green-700 dark:text-green-500' : 'text-gray-900 hover:text-green-700 dark:text-gray-100 dark:hover:text-green-500' }}"
                    @click="open = ! open" :aria-expanded="open">
                    Knockouts
                    <svg class="h-4 w-4 flex-none text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div class="absolute left-0 top-full z-10 mt-3 w-72 rounded-2xl bg-white p-2 shadow-lg ring-1 ring-gray-900/5 dark:bg-zinc-900 dark:ring-white/10"
                    x-show="open"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    data-knockouts-nav>
                    @foreach ($navigableKnockouts as $knockout)
                        <a href="{{ route('knockout.show', $knockout) }}"
                            class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800">
                            {{ $knockout->name }}
                        </a>
                    @endforeach
                    @if ($navigableKnockouts->isNotEmpty())
                        <div class="mx-2 my-1 border-t border-gray-200 dark:border-gray-800"></div>
                    @endif
                    <a href="{{ route('page.show', 'knockout-dates') }}"
                        class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800">
                        Knockout Dates
                    </a>
                </div>
            </div>
            <a href="{{ route('history.index') }}"
                class="text-sm font-semibold leading-6 {{ request()->routeIs('history.*') ? 'text-green-700 dark:text-green-500' : 'text-gray-900 hover:text-green-700 dark:text-gray-100 dark:hover:text-green-500' }}">
                History
            </a>
            <a href="{{ route('page.show', 'handbook') }}"
                class="text-sm font-semibold leading-6 {{ request()->routeIs('page.show') && request()->route('page') === 'handbook' ? 'text-green-700 dark:text-green-500' : 'text-gray-900 hover:text-green-700 dark:text-gray-100 dark:hover:text-green-500' }}">
                Handbook
            </a>
        </div>

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

        <div class="hidden lg:flex lg:flex-1 lg:justify-end lg:gap-x-6">
            <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-300"
                @click="toggleTheme()" aria-label="Toggle dark mode" data-theme-toggle>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" x-cloak x-show="theme !== 'dark'">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                </svg>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" x-cloak x-show="theme === 'dark'">
                    <path fill-rule="evenodd" d="M10 2.75a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0V3.5a.75.75 0 01.75-.75zM10 14a4 4 0 100-8 4 4 0 000 8zm0 3.25a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0V18a.75.75 0 01.75-.75zM4.166 4.166a.75.75 0 011.06 0l1.06 1.061a.75.75 0 11-1.06 1.06L4.166 5.23a.75.75 0 010-1.06zm9.548 9.548a.75.75 0 011.06 0l1.06 1.06a.75.75 0 01-1.06 1.061l-1.06-1.06a.75.75 0 010-1.061zM2.75 10a.75.75 0 01.75-.75H5a.75.75 0 010 1.5H3.5A.75.75 0 012.75 10zm12.25 0a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5H15.75A.75.75 0 0115 10zM5.227 13.713a.75.75 0 011.06 1.061l-1.06 1.06a.75.75 0 11-1.061-1.06l1.061-1.06zm10.607-9.547a.75.75 0 010 1.06l-1.06 1.061a.75.75 0 11-1.061-1.06l1.06-1.061a.75.75 0 011.061 0z" clip-rule="evenodd" />
                </svg>
            </button>
            <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-300"
                data-site-search-trigger aria-label="Open search">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            @if (@auth()->user())
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @click.away="open = false" @close.stop="open = false">
                    <button type="button"
                        class="flex items-center gap-x-1 text-sm font-semibold leading-6 text-gray-900 dark:text-gray-100"
                        aria-expanded="false" @click="open = !open" :aria-expanded="open">
                        {{ auth()->user()->name }}
                        <svg class="h-5 w-5 flex-none text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="absolute right-0 top-full z-10 mt-3 w-56 rounded-xl bg-white p-2 shadow-lg ring-1 ring-gray-900/5 dark:bg-zinc-900 dark:ring-white/10"
                        x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        @click="open = false">
                        <div class="py-1">
                            <a href="{{ route('account.show') }}"
                                class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800">
                                Account
                            </a>
                        </div>
                        <div class="py-1" x-cloak x-show="canInstallApp">
                            <button type="button"
                                class="block w-full rounded-md py-2 pl-3 pr-4 text-left text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800"
                                @click="installApp()"
                                data-install-app-trigger>
                                Install app
                            </button>
                        </div>
                        @if (auth()->user()->is_admin)
                            <div class="py-1">
                                <a href="{{ route('filament.admin.pages.dashboard') }}"
                                    class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800">
                                    Admin
                                </a>
                            </div>
                        @endif
                        @if (app('impersonate')->isImpersonating())
                            <div class="py-1">
                                <a class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800"
                                    href="{{ route('impersonation.leave') }}">
                                    Stop impersonating
                                </a>
                            </div>
                        @endif
                        <div class="py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                    class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    Log out
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <button type="button"
                    class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-100"
                    x-cloak
                    x-show="canInstallApp"
                    @click="installApp()"
                    data-install-app-trigger>
                    Install app
                </button>
                <a href="{{ route('login') }}" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-100">
                    Log in <span aria-hidden="true">&rarr;</span>
                </a>
            @endif
        </div>
    </nav>

    <div class="relative z-50 lg:hidden" role="dialog" aria-modal="true"
        @close.stop="closeMenu()" @keydown.escape.window="closeMenu()" x-cloak x-show="open">
        <div class="fixed inset-x-0 bottom-0 z-20 bg-gray-500/40 transition-opacity dark:bg-black/70" x-show="open"
            @click="closeMenu()"
            :style="`top: ${headerHeight}px; height: calc(100dvh - ${headerHeight}px);`"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div class="fixed inset-x-0 right-0 z-30 bg-white shadow-2xl ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10"
            @click.stop
            :style="`top: ${headerHeight}px; height: calc(100dvh - ${headerHeight}px);`"
            data-mobile-menu-drawer
            x-show="open" x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
            <div class="relative h-full overflow-hidden bg-white dark:bg-zinc-900">
                <div class="absolute inset-0 overflow-y-auto px-4 py-5"
                    x-show="activeDrawer === 'root'"
                    x-cloak
                    data-mobile-menu-panel="root"
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="-translate-x-1/4 opacity-0">
                    <div class="space-y-6">
                        <div class="space-y-3">
                            @foreach ($rulesets as $ruleset)
                                @php
                                    $navigableSections = $ruleset->openSections
                                        ->filter(fn ($section) => filled($section?->getRouteKey()))
                                        ->values();
                                @endphp
                                @continue($navigableSections->isEmpty())
                                <button type="button"
                                    class="flex w-full items-center justify-between rounded-lg px-0 py-3 text-left text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-900"
                                    data-mobile-ruleset-trigger
                                    @click="openDrawer('ruleset-{{ $ruleset->id }}')">
                                    <span>{{ $ruleset->name }}</span>
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @endforeach
                        </div>

                        <div class="space-y-2 border-t border-gray-200 pt-4 dark:border-gray-800">
                            <button type="button"
                                class="flex w-full items-center justify-between rounded-lg px-0 py-3 text-left text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-900"
                                data-mobile-knockouts-trigger
                                @click="openDrawer('knockouts')">
                                <span>Knockouts</span>
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button type="button"
                                class="flex w-full items-center justify-between rounded-lg px-0 py-3 text-left text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-900"
                                data-mobile-history-trigger
                                @click="openDrawer('history')">
                                <span>History</span>
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <a href="{{ route('page.show', 'handbook') }}"
                                class="block rounded-lg px-0 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-900">
                                Handbook
                            </a>
                        </div>

                        <div class="space-y-2 border-t border-gray-200 pt-4 dark:border-gray-800">
                            @if (@auth()->user())
                                <span class="block px-0 text-sm font-semibold leading-7 text-gray-500 dark:text-gray-400">{{ auth()->user()->name }}</span>
                                <a href="{{ route('account.show') }}"
                                    class="block rounded-lg px-0 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-900">
                                    Account
                                </a>
                                @if (auth()->user()->is_admin)
                                    <a href="{{ route('filament.admin.pages.dashboard') }}"
                                        class="block rounded-lg px-0 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-900">
                                        Admin
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}"
                                        class="block rounded-lg px-0 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-900"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        Log out
                                    </a>
                                </form>
                            @else
                                <a href="{{ route('login') }}"
                                    class="block rounded-lg px-0 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-900">
                                    Log in
                                </a>
                            @endif
                            <button type="button"
                                class="block w-full rounded-lg px-0 py-3 text-left text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-900"
                                x-cloak
                                x-show="canInstallApp"
                                @click="installApp()"
                                data-mobile-install-app-trigger>
                                Install app
                            </button>
                        </div>
                    </div>
                </div>

                @foreach ($rulesets as $ruleset)
                    @php
                        $navigableSections = $ruleset->openSections
                            ->filter(fn ($section) => filled($section?->getRouteKey()))
                            ->values();
                    @endphp
                    @continue($navigableSections->isEmpty())
                    <div class="absolute inset-0 overflow-y-auto bg-white px-4 py-5 dark:bg-zinc-900"
                        x-show="activeDrawer === 'ruleset-{{ $ruleset->id }}'"
                        x-cloak
                        data-mobile-ruleset-sections
                        data-mobile-menu-panel="ruleset-{{ $ruleset->id }}"
                        x-transition:enter="transform transition ease-out duration-300"
                        x-transition:enter-start="translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in duration-200"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full">
                        <div class="space-y-6">
                            <button type="button"
                                class="block w-full rounded-lg px-0 py-3 text-left text-base font-semibold leading-7 text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-zinc-900"
                                @click="goBackToRoot()">
                                <span class="flex items-center gap-3">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.78 4.97a.75.75 0 010 1.06L9.06 10l3.72 3.97a.75.75 0 11-1.1 1.02l-4.25-4.5a.75.75 0 010-1.04l4.25-4.5a.75.75 0 011.1-.02z" clip-rule="evenodd" />
                                    </svg>
                                    Back
                                </span>
                            </button>
                            <div class="space-y-2">
                                @foreach ($navigableSections as $section)
                                    <a href="{{ route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section]) }}"
                                        class="block rounded-lg px-0 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-900">
                                        {{ $section->name }}
                                    </a>
                                @endforeach
                                <a href="{{ route('ruleset.show', $ruleset) }}"
                                    class="block rounded-lg px-0 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-900">
                                    {{ $ruleset->name }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="absolute inset-0 overflow-y-auto bg-white px-4 py-5 dark:bg-zinc-900"
                    x-show="activeDrawer === 'history'"
                    x-cloak
                    data-mobile-history-links
                    data-mobile-menu-panel="history"
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full">
                    <div class="space-y-6">
                        <button type="button"
                            class="block w-full rounded-lg px-0 py-3 text-left text-base font-semibold leading-7 text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-zinc-900"
                            @click="goBackToRoot()">
                            <span class="flex items-center gap-3">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.78 4.97a.75.75 0 010 1.06L9.06 10l3.72 3.97a.75.75 0 11-1.1 1.02l-4.25-4.5a.75.75 0 010-1.04l4.25-4.5a.75.75 0 011.1-.02z" clip-rule="evenodd" />
                                </svg>
                                Back
                            </span>
                        </button>
                        <div class="space-y-2">
                            @forelse ($historySeasonGroups as $historySeasonGroup)
                                <button type="button"
                                    class="flex w-full items-center justify-between rounded-lg px-0 py-3 text-left text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-900"
                                    data-mobile-history-season-trigger
                                    @click="openDrawer('history-season-{{ $historySeasonGroup['season']->id }}')">
                                    <span>{{ $historySeasonGroup['season']->name }}</span>
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @empty
                                <span class="block py-3 text-sm text-gray-500 dark:text-gray-400">No historical seasons yet.</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                @foreach ($historySeasonGroups as $historySeasonGroup)
                    <div class="absolute inset-0 overflow-y-auto bg-white px-4 py-5 dark:bg-zinc-900"
                        x-show="activeDrawer === 'history-season-{{ $historySeasonGroup['season']->id }}'"
                        x-cloak
                        data-mobile-history-season-links
                        data-mobile-menu-panel="history-season-{{ $historySeasonGroup['season']->id }}"
                        x-transition:enter="transform transition ease-out duration-300"
                        x-transition:enter-start="translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in duration-200"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full">
                        <div class="space-y-6">
                            <button type="button"
                                class="block w-full rounded-lg px-0 py-3 text-left text-base font-semibold leading-7 text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-zinc-900"
                                @click="openDrawer('history')">
                                <span class="flex items-center gap-3">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.78 4.97a.75.75 0 010 1.06L9.06 10l3.72 3.97a.75.75 0 11-1.1 1.02l-4.25-4.5a.75.75 0 010-1.04l4.25-4.5a.75.75 0 011.1-.02z" clip-rule="evenodd" />
                                    </svg>
                                    Back
                                </span>
                            </button>
                            <div class="space-y-2">
                                @foreach ($historySeasonGroup['rulesets'] as $historyRulesetGroup)
                                    <button type="button"
                                        class="flex w-full items-center justify-between rounded-lg px-0 py-3 text-left text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-900"
                                        data-mobile-history-ruleset-trigger
                                        @click="openDrawer('history-season-{{ $historySeasonGroup['season']->id }}-ruleset-{{ $historyRulesetGroup['ruleset']->id }}')">
                                        <span>{{ $historyRulesetGroup['ruleset']->name }}</span>
                                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @foreach ($historySeasonGroup['rulesets'] as $historyRulesetGroup)
                        <div class="absolute inset-0 overflow-y-auto bg-white px-4 py-5 dark:bg-zinc-900"
                            x-show="activeDrawer === 'history-season-{{ $historySeasonGroup['season']->id }}-ruleset-{{ $historyRulesetGroup['ruleset']->id }}'"
                            x-cloak
                            data-mobile-history-section-links
                            data-mobile-menu-panel="history-season-{{ $historySeasonGroup['season']->id }}-ruleset-{{ $historyRulesetGroup['ruleset']->id }}"
                            x-transition:enter="transform transition ease-out duration-300"
                            x-transition:enter-start="translate-x-full"
                            x-transition:enter-end="translate-x-0"
                            x-transition:leave="transform transition ease-in duration-200"
                            x-transition:leave-start="translate-x-0"
                            x-transition:leave-end="translate-x-full">
                            <div class="space-y-6">
                                <button type="button"
                                    class="block w-full rounded-lg px-0 py-3 text-left text-base font-semibold leading-7 text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-zinc-900"
                                    @click="openDrawer('history-season-{{ $historySeasonGroup['season']->id }}')">
                                    <span class="flex items-center gap-3">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M12.78 4.97a.75.75 0 010 1.06L9.06 10l3.72 3.97a.75.75 0 11-1.1 1.02l-4.25-4.5a.75.75 0 010-1.04l4.25-4.5a.75.75 0 011.1-.02z" clip-rule="evenodd" />
                                        </svg>
                                        Back
                                    </span>
                                </button>
                                <div class="space-y-2">
                                    @foreach ($historyRulesetGroup['sections'] as $historySection)
                                        <a href="{{ route('history.section.show', ['season' => $historySeasonGroup['season'], 'ruleset' => $historyRulesetGroup['ruleset'], 'section' => $historySection]) }}"
                                            class="block rounded-lg px-0 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-900">
                                            {{ $historySection->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach

                <div class="absolute inset-0 overflow-y-auto bg-white px-4 py-5 dark:bg-zinc-900"
                    x-show="activeDrawer === 'knockouts'"
                    x-cloak
                    data-mobile-knockouts-links
                    data-mobile-menu-panel="knockouts"
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full">
                    <div class="space-y-6">
                        <button type="button"
                            class="block w-full rounded-lg px-0 py-3 text-left text-base font-semibold leading-7 text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-zinc-900"
                            @click="goBackToRoot()">
                            <span class="flex items-center gap-3">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.78 4.97a.75.75 0 010 1.06L9.06 10l3.72 3.97a.75.75 0 11-1.1 1.02l-4.25-4.5a.75.75 0 010-1.04l4.25-4.5a.75.75 0 011.1-.02z" clip-rule="evenodd" />
                                </svg>
                                Back
                            </span>
                        </button>
                        <div class="space-y-2">
                            @foreach ($navigableKnockouts as $knockout)
                                <a href="{{ route('knockout.show', $knockout) }}"
                                    class="block rounded-lg px-0 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-900">
                                    {{ $knockout->name }}
                                </a>
                            @endforeach
                            <a href="{{ route('page.show', 'knockout-dates') }}"
                                class="block rounded-lg px-0 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-900">
                                Knockout Dates
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            if (window.siteSearchBindingsRegistered) {
                return;
            }

            window.siteSearchBindingsRegistered = true;

            const openSiteSearch = (event = null) => {
                if (event) {
                    event.preventDefault();
                }

                Livewire.dispatch('openSearch');
            };

            document.querySelectorAll('[data-site-search-trigger]').forEach((trigger) => {
                trigger.addEventListener('click', openSiteSearch);
            });

            document.addEventListener('keydown', (event) => {
                if (! (event.metaKey || event.ctrlKey)) {
                    return;
                }

                if (event.key.toLowerCase() !== 'k') {
                    return;
                }

                openSiteSearch(event);
            });
        });
    </script>
</header>
