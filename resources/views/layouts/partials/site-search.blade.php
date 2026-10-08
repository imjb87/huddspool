<div
    class="relative z-99 duration-300"
    role="dialog"
    aria-modal="true"
    aria-labelledby="site-search-dialog-title"
    x-data="window.createSiteSearch({
        endpoint: @js(route('search.index')),
        moduleUrl: @js(Vite::asset('resources/js/site-search-modal.js')),
    })"
    x-on:site-search:open.window="openSearch()"
    x-on:keydown.escape.window="if (open) { close() }"
    x-cloak
>
    <div
        class="fixed inset-0 bg-gray-500/25 transition-opacity dark:bg-black/70"
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
        class="pointer-events-none fixed inset-0 z-10 flex items-start justify-center overflow-y-auto p-2 sm:items-center"
        :class="open ? 'pointer-events-auto' : 'pointer-events-none'"
    >
        <div
            @click.outside="close()"
            x-show="open"
            x-transition:enter="ui-motion-search-shell-in"
            x-transition:leave="ui-motion-search-shell-out"
            class="relative mx-auto w-full max-w-none overflow-hidden rounded-xl border border-gray-200/80 bg-white p-2 pb-11 text-gray-900 shadow-2xl shadow-black/10 ring-4 ring-gray-200/80 sm:max-w-lg dark:border-neutral-800 dark:bg-neutral-900 dark:text-gray-100 dark:ring-neutral-800"
            data-search-modal-shell
        >
            <h2 id="site-search-dialog-title" class="sr-only">Site search</h2>
            <div class="relative flex h-9 items-center rounded-md border border-gray-200 bg-gray-50/70 px-3 dark:border-neutral-800 dark:bg-neutral-800/50">
                <svg
                    class="pointer-events-none mr-2 size-4 shrink-0 text-gray-400 dark:text-gray-500"
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
                    class="h-9 min-w-0 flex-1 border-0 bg-transparent px-0 text-sm text-gray-900 placeholder:text-gray-600 focus:ring-0 dark:text-gray-100 dark:placeholder:text-gray-400"
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

            <div class="min-h-80 w-full" x-show="isLoading" data-search-loading-state>
                <div class="w-full space-y-1" data-search-loading-skeleton aria-hidden="true">
                    @foreach (['players', 'teams'] as $groupName)
                        <div class="w-full">
                            <div class="px-3 pt-3 pb-1">
                                <div class="h-3 w-20 animate-pulse rounded-md bg-gray-200/80 dark:bg-neutral-800/80"></div>
                            </div>
                            <div class="space-y-0.5">
                                @foreach (range(1, 3) as $rowIndex)
                                    <div class="flex h-9 items-center justify-between gap-4 rounded-md border border-transparent px-3">
                                        <div class="flex min-w-0 flex-1 items-center gap-2">
                                            @if ($groupName === 'players')
                                                <div class="size-6 shrink-0 animate-pulse rounded-full bg-gray-200/80 dark:bg-neutral-800/80"></div>
                                            @endif
                                            <div class="h-3.5 w-32 animate-pulse rounded-md bg-gray-200/80 dark:bg-neutral-800/80 sm:w-40"></div>
                                        </div>
                                        <div class="h-3 w-24 animate-pulse rounded-md bg-gray-200/80 dark:bg-neutral-800/80 sm:w-28"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div x-show="!isLoading && searchTerm.trim().length < 3" data-search-empty-prompt>
                <x-ui-empty-state
                    layout="search"
                    title="Search for players, teams and venues"
                    description="Search players, teams, and venues by name."
                />
            </div>

            <div x-show="!isLoading && searchTerm.trim().length >= 3 && resultGroups.length === 0" data-search-no-results>
                <x-ui-empty-state
                    layout="search"
                    title="No results found"
                    description="No players, teams, or venues matched that search. Try a different name."
                />
            </div>

            <ul
                x-show="!isLoading && resultGroups.length > 0"
                class="min-h-80 max-h-[28rem] overflow-y-auto scroll-py-1.5"
                id="search-results"
                role="listbox"
                data-search-results-shell
            >
                <template x-for="group in resultGroups" :key="group.key">
                    <li>
                        <div class="px-3 pt-3 pb-1">
                            <h2 class="text-xs font-medium text-gray-500 dark:text-gray-400" x-text="group.heading"></h2>
                        </div>
                        <div class="space-y-0.5 pb-1.5" data-search-result-group>
                            <template x-for="item in group.results" :key="`${group.key}-${item.id}`">
                                <a
                                    class="flex h-9 w-full items-center justify-between gap-4 rounded-md border border-transparent px-3 text-sm font-medium outline-none transition-colors hover:bg-gray-100 hover:text-gray-900 focus:bg-gray-100 focus:text-gray-900 dark:hover:bg-neutral-800 dark:hover:text-gray-100 dark:focus:bg-neutral-800 dark:focus:text-gray-100"
                                    :id="`site-search-result-${group.key}-${item.id}`"
                                    :href="item.href"
                                    :class="{ 'border-gray-200 bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800/60': activeResultId() === `site-search-result-${group.key}-${item.id}` }"
                                    data-search-result-link
                                    @mouseenter="setActiveResultById(`site-search-result-${group.key}-${item.id}`)"
                                    @click="close()"
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

            <div class="absolute inset-x-0 bottom-0 z-20 flex h-10 items-center gap-3 rounded-b-xl border-t border-gray-200 bg-gray-50 px-4 text-xs font-medium text-gray-500 dark:border-neutral-800 dark:bg-neutral-800 dark:text-neutral-400">
                <div class="flex items-center gap-1.5">
                    <kbd class="pointer-events-none flex h-5 items-center justify-center gap-1 rounded border border-gray-200 bg-white px-1 font-sans text-[0.7rem] font-medium text-gray-500 shadow-sm select-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">↑↓</kbd>
                    <span>Navigate</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <kbd class="pointer-events-none flex h-5 items-center justify-center rounded border border-gray-200 bg-white px-1 font-sans text-[0.7rem] font-medium text-gray-500 shadow-sm select-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">↵</kbd>
                    <span>Open</span>
                </div>
                <div class="ml-auto flex items-center gap-1.5">
                    <kbd class="pointer-events-none flex h-5 items-center justify-center rounded border border-gray-200 bg-white px-1 font-sans text-[0.7rem] font-medium text-gray-500 shadow-sm select-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">Esc</kbd>
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
                scrollActiveResultIntoView() {},
                initializeSiteSearch() {},
                openLoadedSearch() {
                    this.open = true;
                    this.searchTerm = '';
                    this.resultGroups = [];
                    this.activeResultIndex = -1;
                    this.isLoading = false;
                    this.focusInput();
                },
                closeLoadedSearch() {
                    this.open = false;
                    this.searchTerm = '';
                    this.resultGroups = [];
                    this.activeResultIndex = -1;
                    this.isLoading = false;
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
                    await this.ensureEnhanced();
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

        const dispatchSiteSearchOpen = (event = null) => {
            if (event) {
                event.preventDefault();
            }

            window.dispatchEvent(new CustomEvent('site-search:open'));
        };

        document.addEventListener('click', (event) => {
            const trigger = event.target.closest('[data-site-search-trigger]');

            if (!trigger) {
                return;
            }

            dispatchSiteSearchOpen(event);
        });

        document.addEventListener('keydown', (event) => {
            if (!(event.metaKey || event.ctrlKey)) {
                return;
            }

            if (event.key.toLowerCase() !== 'k') {
                return;
            }

            dispatchSiteSearchOpen(event);
        });
    }
</script>
