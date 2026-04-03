<div class="hidden lg:ml-12 lg:flex lg:items-center lg:gap-x-6">
    @foreach ($navigationRulesets as $navigationRuleset)
        <div class="relative"
            x-data="{
                id: 'ruleset-{{ $navigationRuleset['ruleset']->id }}',
                open: false,
                prefersTap() {
                    return window.matchMedia('(hover: none), (pointer: coarse)').matches;
                },
                show() {
                    this.open = true;
                    this.$dispatch('nav-dropdown-open', { id: this.id });
                },
                openOnHover() {
                    if (! this.prefersTap()) {
                        this.show();
                    }
                },
                closeOnHover() {
                    if (! this.prefersTap()) {
                        this.open = false;
                    }
                },
                toggle() {
                    if (this.open) {
                        this.open = false;

                        return;
                    }

                    this.show();
                },
            }"
            @mouseenter="openOnHover()"
            @mouseleave="closeOnHover()"
            @nav-dropdown-open.window="if ($event.detail.id !== id) open = false">
            <button type="button"
                class="flex items-center gap-x-1 text-sm font-semibold leading-6 transition {{ $navigationRuleset['is_active'] ? 'text-green-700 dark:text-green-500' : 'text-neutral-900 hover:text-green-700 dark:text-neutral-100 dark:hover:text-green-500' }}"
                @click="toggle()" :aria-expanded="open">
                {{ $navigationRuleset['ruleset']->name }}
                <svg class="h-4 w-4 flex-none text-neutral-400 dark:text-neutral-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>

            <div class="absolute left-0 top-full z-10 mt-3 w-72"
                x-show="open"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1">
                <div class="ui-card overflow-hidden">
                    <div class="ui-card-rows">
                        @foreach ($navigationRuleset['sections'] as $section)
                            <a href="{{ route('ruleset.section.show', ['ruleset' => $navigationRuleset['ruleset'], 'section' => $section]) }}"
                                class="ui-card-row-link">
                                <div class="ui-card-row justify-start gap-3 px-4 text-sm font-semibold text-neutral-900 dark:text-neutral-100 sm:px-5">
                                    <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                                        </svg>
                                    </span>
                                    <span>{{ $section->name }}</span>
                                </div>
                            </a>
                        @endforeach
                        <a href="{{ route('ruleset.rules', $navigationRuleset['ruleset']) }}"
                            class="ui-card-row-link">
                            <div class="ui-card-row justify-start gap-3 px-4 text-sm font-semibold text-neutral-900 dark:text-neutral-100 sm:px-5">
                                <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5A2.25 2.25 0 0 1 6 2.25h9.75A2.25 2.25 0 0 1 18 4.5v15.75a.75.75 0 0 1-1.28.53l-1.94-1.94a.75.75 0 0 0-1.06 0l-1.94 1.94a.75.75 0 0 1-1.06 0l-1.94-1.94a.75.75 0 0 0-1.06 0l-1.94 1.94A.75.75 0 0 1 3.75 20.25V4.5Z" />
                                    </svg>
                                </span>
                                <span>{{ $navigationRuleset['ruleset']->name }}</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="relative"
        x-data="{
            id: 'knockouts',
            open: false,
            prefersTap() {
                return window.matchMedia('(hover: none), (pointer: coarse)').matches;
            },
            show() {
                this.open = true;
                this.$dispatch('nav-dropdown-open', { id: this.id });
            },
            openOnHover() {
                if (! this.prefersTap()) {
                    this.show();
                }
            },
            closeOnHover() {
                if (! this.prefersTap()) {
                    this.open = false;
                }
            },
            toggle() {
                if (this.open) {
                    this.open = false;

                    return;
                }

                this.show();
            },
        }"
        @mouseenter="openOnHover()"
        @mouseleave="closeOnHover()"
        @nav-dropdown-open.window="if ($event.detail.id !== id) open = false">
        <button type="button"
            class="flex items-center gap-x-1 text-sm font-semibold leading-6 transition {{ $knockoutNavIsActive ? 'text-green-700 dark:text-green-500' : 'text-neutral-900 hover:text-green-700 dark:text-neutral-100 dark:hover:text-green-500' }}"
            @click="toggle()" :aria-expanded="open">
            Knockouts
            <svg class="h-4 w-4 flex-none text-neutral-400 dark:text-neutral-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        </button>

        <div class="absolute left-0 top-full z-10 mt-3 w-72"
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            data-knockouts-nav>
            <div class="ui-card overflow-hidden dark:bg-neutral-900">
                <div class="ui-card-rows">
                    @foreach ($navigableKnockouts as $knockout)
                        <a href="{{ route('knockout.show', $knockout) }}"
                            class="ui-card-row-link">
                            <div class="ui-card-row justify-start gap-3 px-4 text-sm font-semibold text-neutral-900 dark:text-neutral-100 sm:px-5">
                                <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                                    </svg>
                                </span>
                                <span>{{ $knockout->name }}</span>
                            </div>
                        </a>
                    @endforeach
                    <a href="{{ route('page.show', 'knockout-dates') }}"
                        class="ui-card-row-link">
                        <div class="ui-card-row justify-start gap-3 px-4 text-sm font-semibold text-neutral-900 dark:text-neutral-100 sm:px-5">
                            <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5A2.25 2.25 0 0 1 6 2.25h9.75A2.25 2.25 0 0 1 18 4.5v15.75a.75.75 0 0 1-1.28.53l-1.94-1.94a.75.75 0 0 0-1.06 0l-1.94 1.94a.75.75 0 0 1-1.06 0l-1.94-1.94a.75.75 0 0 0-1.06 0l-1.94 1.94A.75.75 0 0 1 3.75 20.25V4.5Z" />
                                </svg>
                            </span>
                            <span>Knockout Dates</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('history.index') }}"
        class="text-sm font-semibold leading-6 transition {{ $historyNavIsActive ? 'text-green-700 dark:text-green-500' : 'text-neutral-900 hover:text-green-700 dark:text-neutral-100 dark:hover:text-green-500' }}">
        History
    </a>
    <a href="{{ route('news.index') }}"
        class="text-sm font-semibold leading-6 transition {{ request()->routeIs('news.index', 'news.show') ? 'text-green-700 dark:text-green-500' : 'text-neutral-900 hover:text-green-700 dark:text-neutral-100 dark:hover:text-green-500' }}">
        News
    </a>
</div>
