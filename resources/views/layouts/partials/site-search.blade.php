<div
    class="fixed inset-x-0 z-40"
    style="top: var(--site-search-visible-top, var(--site-header-height, 4rem)); height: var(--site-search-panel-height, calc(100dvh - var(--site-header-height, 4rem)));"
    :class="open ? 'pointer-events-auto' : 'pointer-events-none'"
    role="dialog"
    aria-modal="true"
    :aria-hidden="!open"
    :inert="!open"
    aria-labelledby="site-search-dialog-title"
    x-data="window.createSiteSearch({
        endpoint: @js(route('search.index')),
        moduleUrl: @js(Vite::asset('resources/js/site-search-modal.js')),
    })"
    x-init="initializeSearchViewport()"
    x-on:site-search:open.window="openSearch()"
    x-on:site-search:toggle.window="toggleSearch()"
    x-on:header-overlay-open.window="if ($event.detail.id !== 'search' && open) close()"
    x-on:keydown.escape.window="if (open) { close() }"
    x-cloak
>
    <div
        class="absolute inset-0 z-0 bg-black/20 transition-opacity dark:bg-black/60"
        x-show="open"
        x-transition:enter="ui-motion-fade-in"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ui-motion-fade-out"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        aria-hidden="true"
        @click="close()"
    ></div>

    <div
        class="absolute inset-0 z-10 flex overflow-hidden"
        @click="close()"
    >
        <div
            x-show="open"
            @click.stop
            x-transition:enter="ui-motion-mobile-menu-in"
            x-transition:leave="ui-motion-mobile-menu-out"
            class="relative flex h-full w-full flex-col overflow-hidden bg-background text-foreground"
            data-search-modal-shell
        >
            <h2 id="site-search-dialog-title" class="sr-only">Site search</h2>
            <div class="relative mx-2 mt-2 flex h-9 shrink-0 items-center rounded-md border border-border/70 bg-muted/50 px-3">
                <svg
                    class="pointer-events-none mr-2 size-4 shrink-0 text-muted-foreground"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <circle cx="11" cy="11" r="7" />
                    <path d="m20 20-4-4" />
                </svg>
                <input
                    type="text"
                    id="searchInput"
                    x-ref="searchInput"
                    x-model="searchTerm"
                    autocomplete="off"
                    class="h-9 min-w-0 flex-1 border-0 bg-transparent px-0 text-sm text-foreground placeholder:text-muted-foreground focus:ring-0"
                    placeholder="Search players, teams, venues..."
                    role="combobox"
                    :aria-expanded="resultGroups.length > 0 ? 'true' : 'false'"
                    :aria-activedescendant="activeResultId()"
                    aria-controls="search-results"
                    @keydown.arrow-down.prevent="moveActiveResult(1)"
                    @keydown.arrow-up.prevent="moveActiveResult(-1)"
                    @keydown.enter.prevent="openActiveResult()"
                >
            </div>

            <div class="min-h-0 w-full flex-1 overflow-y-auto p-2" x-show="isLoading" data-search-loading-state>
                <div class="w-full space-y-1" data-search-loading-skeleton aria-hidden="true">
                    @foreach (['players', 'teams'] as $groupName)
                        <div class="w-full">
                            <div class="px-3 pt-3 pb-1">
                                <div class="h-3 w-20 animate-pulse rounded-md bg-muted"></div>
                            </div>
                            <div class="space-y-0.5">
                                @foreach (range(1, 3) as $rowIndex)
                                    <div class="flex h-9 items-center justify-between gap-4 rounded-md border border-border/50 bg-muted/40 px-3">
                                        <div class="flex min-w-0 flex-1 items-center gap-2">
                                            @if ($groupName === 'players')
                                                <div class="size-6 shrink-0 animate-pulse rounded-full bg-muted"></div>
                                            @endif
                                            <div class="h-3.5 w-32 animate-pulse rounded-md bg-muted sm:w-40"></div>
                                        </div>
                                        <div class="h-3 w-24 animate-pulse rounded-md bg-muted sm:w-28"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto" x-show="!isLoading && searchTerm.trim().length < 3" data-search-empty-prompt>
                <x-ui-empty-state
                    layout="search"
                    title="Search for players, teams and venues"
                    description="Search players, teams, and venues by name."
                />
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto" x-show="!isLoading && searchTerm.trim().length >= 3 && resultGroups.length === 0" data-search-no-results>
                <x-ui-empty-state
                    layout="search"
                    title="No results found"
                    description="No players, teams, or venues matched that search. Try a different name."
                />
            </div>

            <ul
                x-show="!isLoading && resultGroups.length > 0"
                class="min-h-0 flex-1 overflow-y-auto scroll-py-1.5 px-2 pb-2"
                id="search-results"
                role="listbox"
                data-search-results-shell
            >
                <template x-for="group in resultGroups" :key="group.key">
                    <li>
                        <div class="px-3 pt-3 pb-1">
                            <h2 class="text-xs font-medium text-muted-foreground" x-text="group.heading"></h2>
                        </div>
                        <div class="mx-3 space-y-2 pb-2" data-search-result-group>
                            <template x-for="item in group.results" :key="`${group.key}-${item.id}`">
                                <a
                                    class="flex h-9 w-full items-center justify-between gap-4 rounded-md border border-border/50 bg-muted/40 px-3 text-sm font-medium text-foreground outline-none transition-colors hover:bg-muted focus:bg-muted"
                                    :id="`site-search-result-${group.key}-${item.id}`"
                                    :href="item.href"
                                    :class="{ 'border-border/70 bg-muted': activeResultId() === `site-search-result-${group.key}-${item.id}` }"
                                    data-search-result-link
                                    @mouseenter="setActiveResultById(`site-search-result-${group.key}-${item.id}`)"
                                    @click="navigateToResult($event)"
                                >
                                    <div class="flex min-w-0 flex-1 items-center gap-2">
                                        <template x-if="group.key === 'players'">
                                            <span class="relative flex size-6 shrink-0 overflow-hidden rounded-full bg-gray-100 select-none dark:bg-neutral-800" data-search-player-avatar aria-hidden="true">
                                                <img :src="item.avatarUrl" alt="" class="aspect-square size-full object-cover">
                                            </span>
                                        </template>
                                        <p class="min-w-0 truncate text-sm font-medium text-gray-900 dark:text-gray-100" x-text="item.name"></p>
                                    </div>
                                    <p class="max-w-[45%] shrink-0 truncate text-xs font-normal text-gray-500 dark:text-gray-400" x-text="item.secondaryText"></p>
                                </a>
                            </template>
                        </div>
                    </li>
                </template>
            </ul>

            <div class="mt-auto flex h-10 shrink-0 items-center gap-3 border-t border-border/70 bg-muted/50 px-4 text-xs font-medium text-muted-foreground">
                <div class="flex items-center gap-1.5">
                    <kbd class="pointer-events-none flex h-5 items-center justify-center gap-1 rounded border border-border/70 bg-background px-1 font-sans text-[0.7rem] font-medium text-gray-500 shadow-sm select-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">↑↓</kbd>
                    <span>Navigate</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <kbd class="pointer-events-none flex h-5 items-center justify-center rounded border border-border/70 bg-background px-1 font-sans text-[0.7rem] font-medium text-gray-500 shadow-sm select-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">↵</kbd>
                    <span>Open</span>
                </div>
                <div class="ml-auto flex items-center gap-1.5">
                    <kbd class="pointer-events-none flex h-5 items-center justify-center rounded border border-border/70 bg-background px-1 font-sans text-[0.7rem] font-medium text-gray-500 shadow-sm select-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">Esc</kbd>
                    <span>Close</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    if (!window.createSiteSearch) {
        window.createSiteSearch = function ({ endpoint, moduleUrl }) {
            return {
                endpoint,
                moduleUrl,
                open: false,
                searchTerm: '',
                resultGroups: [],
                isLoading: false,
                focusTimer: null,
                searchTimer: null,
                viewportFrameId: null,
                abortController: null,
                activeResultIndex: -1,
                isEnhanced: false,
                flattenedResults() {
                    return [];
                },
                activeResult() {
                    return null;
                },
                activeResultId() {
                    return null;
                },
                syncActiveResult() {},
                setActiveResultById() {},
                moveActiveResult() {},
                openActiveResult() {},
                navigateToResult() {},
                scrollActiveResultIntoView() {},
                initializeSiteSearch() {},
                syncSearchViewport() {
                    const viewport = window.visualViewport;
                    const headerHeight = document.querySelector('.site-header')?.getBoundingClientRect().height
                        || Number.parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--site-header-height'))
                        || 64;
                    const visibleHeight = viewport?.height ?? window.innerHeight;
                    const visibleTop = (viewport?.offsetTop ?? 0) + headerHeight;

                    document.documentElement.style.setProperty('--site-search-visible-top', `${visibleTop}px`);
                    document.documentElement.style.setProperty('--site-search-panel-height', `${Math.max(0, visibleHeight - headerHeight)}px`);
                },
                scheduleSearchViewportUpdate() {
                    if (this.viewportFrameId !== null) {
                        window.cancelAnimationFrame(this.viewportFrameId);
                    }

                    this.viewportFrameId = window.requestAnimationFrame(() => {
                        this.viewportFrameId = null;
                        this.syncSearchViewport();
                    });
                },
                initializeSearchViewport() {
                    this.syncSearchViewport();
                    window.visualViewport?.addEventListener('resize', () => this.scheduleSearchViewportUpdate());
                    window.visualViewport?.addEventListener('scroll', () => this.scheduleSearchViewportUpdate());
                    window.addEventListener('resize', () => this.scheduleSearchViewportUpdate());
                },
                syncSearchTrigger() {
                    const trigger = document.querySelector('[data-site-search-trigger]');

                    if (!trigger) {
                        return;
                    }

                    trigger.setAttribute('aria-expanded', this.open ? 'true' : 'false');
                    trigger.setAttribute('aria-label', this.open ? 'Close search' : 'Open search');

                    const label = trigger.querySelector('[data-search-trigger-label]');

                    if (label) {
                        label.textContent = this.open ? 'Close' : 'Search...';
                    }

                    window.headerActionIcon?.set(
                        trigger.querySelector('[data-header-action-icon="search"]'),
                        this.open,
                    );
                },
                toggleSearch() {
                    if (this.open) {
                        this.close();

                        return;
                    }

                    this.openSearch();
                },
                openLoadedSearch() {
                    this.open = true;
                    this.searchTerm = '';
                    this.resultGroups = [];
                    this.activeResultIndex = -1;
                    this.isLoading = false;
                    this.syncSearchTrigger();
                    this.focusInput();
                },
                closeLoadedSearch() {
                    this.open = false;
                    this.searchTerm = '';
                    this.resultGroups = [];
                    this.activeResultIndex = -1;
                    this.isLoading = false;
                    this.syncSearchTrigger();
                },
                async ensureEnhanced() {
                    if (this.isEnhanced) {
                        return;
                    }

                    await import(this.moduleUrl);
                    const enhanceSiteSearch = window.enhanceSiteSearch;

                    if (typeof enhanceSiteSearch !== 'function') {
                        throw new Error('Site search enhancer failed to load.');
                    }

                    enhanceSiteSearch(this);
                    this.isEnhanced = true;
                    this.initializeSiteSearch();
                },
                async openSearch() {
                    window.dispatchEvent(new CustomEvent('header-overlay-open', { detail: { id: 'search' } }));
                    this.open = true;
                    this.syncSearchTrigger();
                    await this.ensureEnhanced();

                    if (!this.open) {
                        return;
                    }

                    this.openLoadedSearch();
                },
                close() {
                    if (!this.isEnhanced) {
                        this.closeLoadedSearch();
                        return;
                    }

                    this.closeLoadedSearch();
                },
            };
        };
    }

    if (!window.siteSearchBindingsRegistered) {
        window.siteSearchBindingsRegistered = true;

        const dispatchSiteSearchEvent = (name, event = null) => {
            if (event) {
                event.preventDefault();
            }

            window.dispatchEvent(new CustomEvent(name));
        };

        document.addEventListener('keydown', (event) => {
            if (!(event.metaKey || event.ctrlKey)) {
                return;
            }

            if (event.key.toLowerCase() !== 'k') {
                return;
            }

            dispatchSiteSearchEvent('site-search:open', event);
        });
    }
</script>
