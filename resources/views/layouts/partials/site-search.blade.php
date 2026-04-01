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
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        aria-hidden="true"
        @click="close()"
    ></div>

    <div
        class="fixed inset-0 z-10 overflow-y-auto px-4 py-[12px] sm:p-6 md:p-20"
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
    >
        <div
            @click.outside="close()"
            class="ui-card mx-auto max-w-xl transform transition-all dark:bg-neutral-900"
            data-search-modal-shell
        >
            <h2 id="site-search-dialog-title" class="sr-only">Site search</h2>
            <div class="relative border-b border-gray-200 dark:border-neutral-800/80">
                <svg
                    class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-gray-400 dark:text-gray-500"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                        clip-rule="evenodd"
                    />
                </svg>
                <input
                    type="search"
                    id="searchInput"
                    x-ref="searchInput"
                    x-model="searchTerm"
                    autocomplete="off"
                    class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 dark:text-gray-100 dark:placeholder:text-gray-500 sm:pr-24 sm:text-sm"
                    placeholder="Search players, teams, venues..."
                    role="combobox"
                    :aria-expanded="resultGroups.length > 0 ? 'true' : 'false'"
                    :aria-activedescendant="activeResultId()"
                    aria-controls="search-results"
                    @keydown.arrow-down.prevent="moveActiveResult(1)"
                    @keydown.arrow-up.prevent="moveActiveResult(-1)"
                    @keydown.enter.prevent="openActiveResult()"
                >
                <div class="pointer-events-none absolute inset-y-0 right-4 hidden items-center sm:flex">
                    <span class="rounded-md border border-gray-200 bg-gray-50 px-2 py-1 text-[11px] font-semibold tracking-wide text-gray-400 dark:border-neutral-800 dark:bg-neutral-800 dark:text-gray-500">
                        Ctrl K
                    </span>
                </div>
            </div>

            <div class="w-full" x-show="isLoading" data-search-loading-state>
                <div class="w-full space-y-3" data-search-loading-skeleton>
                    @foreach (range(1, 2) as $groupIndex)
                        <div class="w-full border-t border-gray-200/80 first:border-t-0 dark:border-neutral-800/75">
                            <div class="ui-card-column-headings justify-start px-4 sm:px-5">
                                <div class="h-3 w-20 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                            </div>
                            <div class="ui-card-rows">
                                @foreach (range(1, 3) as $rowIndex)
                                    <div class="ui-card-row items-start">
                                        <div class="min-w-0 flex flex-1 items-start gap-3">
                                            <div class="h-9 w-9 shrink-0 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                            <div class="min-w-0 flex-1 space-y-2">
                                                <div class="h-3.5 w-32 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-40"></div>
                                                <div class="h-3 w-24 rounded-full bg-gray-100 dark:bg-neutral-900/70 sm:w-28"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div x-show="!isLoading && searchTerm.trim().length < 3" data-search-empty-prompt>
                <div class="px-6 py-14 text-center text-sm sm:px-14">
                    <svg class="mx-auto h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.115 5.19l.319 1.913A6 6 0 008.11 10.36L9.75 12l-.387.775c-.217.433-.132.956.21 1.298l1.348 1.348c.21.21.329.497.329.795v1.089c0 .426.24.815.622 1.006l.153.076c.433.217.956.132 1.298-.21l.723-.723a8.7 8.7 0 002.288-4.042 1.087 1.087 0 00-.358-1.099l-1.33-1.108c-.251-.21-.582-.299-.905-.245l-1.17.195a1.125 1.125 0 01-.98-.314l-.295-.295a1.125 1.125 0 010-1.591l.13-.132a1.125 1.125 0 011.3-.21l.603.302a.809.809 0 001.086-1.086L14.25 7.5l1.256-.837a4.5 4.5 0 001.528-1.732l.146-.292M6.115 5.19A9 9 0 1017.18 4.64M6.115 5.19A8.965 8.965 0 0112 3c1.929 0 3.716.607 5.18 1.64" />
                    </svg>
                    <p class="mt-4 font-semibold text-gray-900 dark:text-gray-100">Search for players, teams and venues</p>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Quickly find what you’re looking for by running a global search.</p>
                </div>
            </div>

            <div x-show="!isLoading && searchTerm.trim().length >= 3 && resultGroups.length === 0" data-search-no-results>
                <div class="px-6 py-14 text-center text-sm sm:px-14">
                    <svg class="mx-auto h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
                    </svg>
                    <p class="mt-4 font-semibold text-gray-900 dark:text-gray-100">No results found</p>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">We couldn’t find anything with that term. Please try again.</p>
                </div>
            </div>

            <ul
                x-show="!isLoading && resultGroups.length > 0"
                class="max-h-[28rem] overflow-y-auto"
                id="search-results"
                role="listbox"
                data-search-results-shell
            >
                <template x-for="group in resultGroups" :key="group.key">
                    <li class="border-t border-gray-200/80 first:border-t-0 dark:border-neutral-800/75">
                        <div class="ui-card-column-headings justify-start px-4 sm:px-5">
                            <h2 class="text-xs font-medium text-gray-500 dark:text-gray-400" x-text="group.heading"></h2>
                        </div>
                        <div class="ui-card-rows" data-search-result-group>
                            <template x-for="item in group.results" :key="`${group.key}-${item.id}`">
                                <a
                                    class="ui-card-row-link focus:outline-none"
                                    :id="`site-search-result-${group.key}-${item.id}`"
                                    :href="item.href"
                                    :class="{ 'bg-gray-50 dark:bg-neutral-900/60': activeResultId() === `site-search-result-${group.key}-${item.id}` }"
                                    data-search-result-link
                                    @mouseenter="setActiveResultById(`site-search-result-${group.key}-${item.id}`)"
                                    @click="close()"
                                >
                                    <div class="ui-card-row items-start">
                                        <template x-if="group.key === 'players'">
                                            <img
                                                :src="item.avatarUrl"
                                                :alt="item.name"
                                                class="mt-0.5 h-9 w-9 shrink-0 rounded-full object-cover ring-1 ring-gray-200 dark:ring-neutral-800"
                                                data-search-player-avatar
                                            >
                                        </template>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="item.name"></p>
                                            <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400" x-text="item.secondaryText"></p>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </li>
                </template>
            </ul>
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
