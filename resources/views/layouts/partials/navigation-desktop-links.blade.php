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
                                <div class="ui-card-row px-4 text-sm font-semibold text-neutral-900 dark:text-neutral-100 sm:px-5">
                                    {{ $section->name }}
                                </div>
                            </a>
                        @endforeach
                        <a href="{{ route('ruleset.show', $navigationRuleset['ruleset']) }}"
                            class="ui-card-row-link">
                            <div class="ui-card-row px-4 text-sm font-semibold text-neutral-900 dark:text-neutral-100 sm:px-5">
                                {{ $navigationRuleset['ruleset']->name }}
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
                            <div class="ui-card-row px-4 text-sm font-semibold text-neutral-900 dark:text-neutral-100 sm:px-5">
                                {{ $knockout->name }}
                            </div>
                        </a>
                    @endforeach
                    <a href="{{ route('page.show', 'knockout-dates') }}"
                        class="ui-card-row-link">
                        <div class="ui-card-row px-4 text-sm font-semibold text-neutral-900 dark:text-neutral-100 sm:px-5">
                            Knockout Dates
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
    <a href="{{ route('page.show', 'handbook') }}"
        class="text-sm font-semibold leading-6 transition {{ $handbookNavIsActive ? 'text-green-700 dark:text-green-500' : 'text-neutral-900 hover:text-green-700 dark:text-neutral-100 dark:hover:text-green-500' }}">
        Handbook
    </a>
</div>
