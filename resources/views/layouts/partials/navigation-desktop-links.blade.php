<div class="relative hidden lg:ml-4 lg:flex lg:items-center lg:gap-1" data-navigation-menu>
    @foreach ($navigationRulesets as $navigationRuleset)
        <div class="relative"
            x-data="{
                id: 'ruleset-{{ $navigationRuleset['ruleset']->id }}',
                open: false,
                closeTimer: null,
                prefersTap() {
                    return window.matchMedia('(hover: none), (pointer: coarse)').matches;
                },
                cancelClose() {
                    if (this.closeTimer) {
                        window.clearTimeout(this.closeTimer);
                        this.closeTimer = null;
                    }
                },
                scheduleClose() {
                    this.cancelClose();
                    this.closeTimer = window.setTimeout(() => {
                        this.open = false;
                        this.closeTimer = null;
                    }, 150);
                },
                show() {
                    this.cancelClose();
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
                        this.scheduleClose();
                    }
                },
                close() {
                    this.cancelClose();
                    this.open = false;
                },
                focusTrigger(position) {
                    const navigationMenu = this.$el.closest('[data-navigation-menu]');
                    const triggers = Array.from(navigationMenu.querySelectorAll('[data-navigation-menu-trigger]'));
                    const currentTrigger = this.$el.querySelector('[data-navigation-menu-trigger]');
                    const currentIndex = triggers.indexOf(currentTrigger);
                    const nextIndex = position === 'first'
                        ? 0
                        : position === 'last'
                            ? triggers.length - 1
                            : (currentIndex + position + triggers.length) % triggers.length;

                    triggers[nextIndex]?.focus();
                },
                toggle() {
                    if (this.open) {
                        this.close();

                        return;
                    }

                    this.show();
                },
            }"
            @mouseenter="openOnHover()"
            @mouseleave="closeOnHover()"
            @focusin="show()"
            @focusout="if (! $el.contains($event.relatedTarget)) scheduleClose()"
            @keydown.escape.stop="close(); $el.querySelector('[data-navigation-menu-trigger]')?.focus()"
            @click.outside="close()"
            @nav-dropdown-open.window="if ($event.detail.id !== id) close()">
            <button type="button"
                class="group inline-flex h-8 w-max items-center justify-center gap-1.5 rounded-md bg-white px-2.5 py-2 text-sm leading-5 font-medium whitespace-nowrap text-gray-900 transition-[color,box-shadow] outline-none hover:bg-gray-100 hover:text-gray-900 focus:bg-gray-100 focus:text-gray-900 focus-visible:ring-2 focus-visible:ring-green-600/40 focus-visible:outline-1 data-[state=open]:bg-gray-100 data-[state=open]:text-gray-900 dark:bg-neutral-950 dark:text-gray-100 dark:hover:bg-neutral-800 dark:hover:text-gray-100 dark:focus:bg-neutral-800 dark:focus:text-gray-100 dark:data-[state=open]:bg-neutral-800 dark:data-[state=open]:text-gray-100 {{ $navigationRuleset['is_active'] ? 'bg-gray-100 text-gray-900 dark:bg-neutral-800 dark:text-gray-100' : '' }}"
                @click="toggle()"
                @keydown.arrowdown.prevent="show(); $nextTick(() => $refs.menuContent.querySelector('a')?.focus())"
                @keydown.arrowright.prevent="focusTrigger(1)"
                @keydown.arrowleft.prevent="focusTrigger(-1)"
                @keydown.home.prevent="focusTrigger('first')"
                @keydown.end.prevent="focusTrigger('last')"
                :aria-expanded="open"
                :data-state="open ? 'open' : 'closed'"
                data-navigation-menu-trigger>
                {{ $navigationRuleset['ruleset']->name }}
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="relative top-px ml-1 size-3 flex-none text-neutral-400 transition duration-300 dark:text-neutral-500" :class="open ? 'rotate-180' : ''" aria-hidden="true">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M6 9l6 6l6 -6" />
                </svg>
            </button>

            <span x-show="open" x-cloak x-transition
                class="absolute left-1/2 top-full z-50 size-2 -translate-x-1/2 translate-y-1/2 rotate-45 bg-white ring-1 ring-gray-200 dark:bg-neutral-900 dark:ring-neutral-800"
                aria-hidden="true"></span>

            <div x-ref="menuContent"
                class="absolute left-0 top-full z-50 mt-1.5 w-72 origin-top-left overflow-hidden rounded-md border border-gray-200 bg-white p-2 pr-2.5 text-gray-900 shadow-lg shadow-black/5 dark:border-neutral-800 dark:bg-neutral-900 dark:text-gray-100 dark:shadow-black/20"
                x-show="open"
                x-cloak
                x-transition:enter="transform transition duration-200 ease-out"
                x-transition:enter-start="translate-y-1 scale-95 opacity-0"
                x-transition:enter-end="translate-y-0 scale-100 opacity-100"
                x-transition:leave="transform transition duration-150 ease-in"
                x-transition:leave-start="translate-y-0 scale-100 opacity-100"
                x-transition:leave-end="translate-y-1 scale-95 opacity-0"
                @mouseenter="cancelClose()"
                data-navigation-menu-content
                :data-state="open ? 'open' : 'closed'">
                <div class="flex flex-col gap-1">
                        @foreach ($navigationRuleset['sections'] as $section)
                            <a href="{{ route('ruleset.section.show', ['ruleset' => $navigationRuleset['ruleset'], 'section' => $section]) }}"
                                class="block rounded-sm transition-colors hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                                <div class="rounded-sm px-2 py-2 text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">
                                    <span>{{ $section->name }}</span>
                                </div>
                            </a>
                        @endforeach
                        <a href="{{ route('ruleset.rules', $navigationRuleset['ruleset']) }}"
                            class="block rounded-sm transition-colors hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                            <div class="rounded-sm px-2 py-2 text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">
                                <span>{{ $navigationRuleset['ruleset']->name }}</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
    @endforeach

    <div class="relative"
        x-data="{
            id: 'knockouts',
            open: false,
            closeTimer: null,
            prefersTap() {
                return window.matchMedia('(hover: none), (pointer: coarse)').matches;
            },
            cancelClose() {
                if (this.closeTimer) {
                    window.clearTimeout(this.closeTimer);
                    this.closeTimer = null;
                }
            },
            scheduleClose() {
                this.cancelClose();
                this.closeTimer = window.setTimeout(() => {
                    this.open = false;
                    this.closeTimer = null;
                }, 150);
            },
            show() {
                this.cancelClose();
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
                    this.scheduleClose();
                }
            },
            close() {
                this.cancelClose();
                this.open = false;
            },
            focusTrigger(position) {
                const navigationMenu = this.$el.closest('[data-navigation-menu]');
                const triggers = Array.from(navigationMenu.querySelectorAll('[data-navigation-menu-trigger]'));
                const currentTrigger = this.$el.querySelector('[data-navigation-menu-trigger]');
                const currentIndex = triggers.indexOf(currentTrigger);
                const nextIndex = position === 'first'
                    ? 0
                    : position === 'last'
                        ? triggers.length - 1
                        : (currentIndex + position + triggers.length) % triggers.length;

                triggers[nextIndex]?.focus();
            },
            toggle() {
                if (this.open) {
                    this.close();

                    return;
                }

                this.show();
            },
        }"
        @mouseenter="openOnHover()"
        @mouseleave="closeOnHover()"
        @focusin="show()"
        @focusout="if (! $el.contains($event.relatedTarget)) scheduleClose()"
        @keydown.escape.stop="close(); $el.querySelector('[data-navigation-menu-trigger]')?.focus()"
        @click.outside="close()"
        @nav-dropdown-open.window="if ($event.detail.id !== id) close()">
        <button type="button"
            class="group inline-flex h-8 w-max items-center justify-center gap-1.5 rounded-md bg-white px-2.5 py-2 text-sm leading-5 font-medium whitespace-nowrap text-gray-900 transition-[color,box-shadow] outline-none hover:bg-gray-100 hover:text-gray-900 focus:bg-gray-100 focus:text-gray-900 focus-visible:ring-2 focus-visible:ring-green-600/40 focus-visible:outline-1 data-[state=open]:bg-gray-100 data-[state=open]:text-gray-900 dark:bg-neutral-950 dark:text-gray-100 dark:hover:bg-neutral-800 dark:hover:text-gray-100 dark:focus:bg-neutral-800 dark:focus:text-gray-100 dark:data-[state=open]:bg-neutral-800 dark:data-[state=open]:text-gray-100 {{ $knockoutNavIsActive ? 'bg-gray-100 text-gray-900 dark:bg-neutral-800 dark:text-gray-100' : '' }}"
            @click="toggle()"
            @keydown.arrowdown.prevent="show(); $nextTick(() => $refs.menuContent.querySelector('a')?.focus())"
            @keydown.arrowright.prevent="focusTrigger(1)"
            @keydown.arrowleft.prevent="focusTrigger(-1)"
            @keydown.home.prevent="focusTrigger('first')"
            @keydown.end.prevent="focusTrigger('last')"
            :aria-expanded="open"
            :data-state="open ? 'open' : 'closed'"
            data-navigation-menu-trigger>
            Knockouts
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="relative top-px ml-1 size-3 flex-none text-neutral-400 transition duration-300 dark:text-neutral-500" :class="open ? 'rotate-180' : ''" aria-hidden="true">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M6 9l6 6l6 -6" />
            </svg>
        </button>

        <span x-show="open" x-cloak x-transition
            class="absolute left-1/2 top-full z-50 size-2 -translate-x-1/2 translate-y-1/2 rotate-45 bg-white ring-1 ring-gray-200 dark:bg-neutral-900 dark:ring-neutral-800"
            aria-hidden="true"></span>

        <div x-ref="menuContent"
            class="absolute left-0 top-full z-50 mt-1.5 w-72 origin-top-left overflow-hidden rounded-md border border-gray-200 bg-white p-2 pr-2.5 text-gray-900 shadow-lg shadow-black/5 dark:border-neutral-800 dark:bg-neutral-900 dark:text-gray-100 dark:shadow-black/20"
            x-show="open"
            x-cloak
            x-transition:enter="transform transition duration-200 ease-out"
            x-transition:enter-start="translate-y-1 scale-95 opacity-0"
            x-transition:enter-end="translate-y-0 scale-100 opacity-100"
            x-transition:leave="transform transition duration-150 ease-in"
            x-transition:leave-start="translate-y-0 scale-100 opacity-100"
            x-transition:leave-end="translate-y-1 scale-95 opacity-0"
            @mouseenter="cancelClose()"
            data-navigation-menu-content
            :data-state="open ? 'open' : 'closed'"
            data-knockouts-nav>
            <div class="flex flex-col gap-1">
                    @foreach ($navigableKnockouts as $knockout)
                        <a href="{{ route('knockout.show', $knockout) }}"
                            class="block rounded-sm transition-colors hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                            <div class="rounded-sm px-2 py-2 text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">
                                <span>{{ $knockout->name }}</span>
                            </div>
                        </a>
                    @endforeach
                    <a href="{{ route('page.show', 'knockout-dates') }}"
                        class="block rounded-sm transition-colors hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                        <div class="rounded-sm px-2 py-2 text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">
                            <span>Knockout Dates</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    @include('layouts.partials.navigation-desktop-official')
    <div class="relative"
        x-data="{
            id: 'history',
            open: false,
            activeHistorySeason: null,
            closeTimer: null,
            prefersTap() {
                return window.matchMedia('(hover: none), (pointer: coarse)').matches;
            },
            cancelClose() {
                if (this.closeTimer) {
                    window.clearTimeout(this.closeTimer);
                    this.closeTimer = null;
                }
            },
            scheduleClose() {
                this.cancelClose();
                this.closeTimer = window.setTimeout(() => {
                    this.open = false;
                    this.closeTimer = null;
                }, 150);
            },
            show() {
                this.cancelClose();
                this.open = true;
                this.$dispatch('nav-dropdown-open', { id: this.id });
                this.$nextTick(() => this.positionMenu());
            },
            positionMenu() {
                const trigger = this.$el.querySelector('[data-navigation-menu-trigger]');
                const menu = this.$refs.menuContent;

                if (! trigger || ! menu) {
                    return;
                }

                const triggerRect = trigger.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                const viewportPadding = 16;
                const menuWidth = Math.min(
                    menu.offsetWidth || 768,
                    viewportWidth - (viewportPadding * 2),
                );
                const triggerCenter = triggerRect.left + (triggerRect.width / 2);
                const minCenter = viewportPadding + (menuWidth / 2);
                const maxCenter = viewportWidth - viewportPadding - (menuWidth / 2);
                const menuCenter = Math.min(Math.max(triggerCenter, minCenter), maxCenter);

                menu.style.left = `${Math.round(menuCenter)}px`;
                menu.style.top = `${Math.round(triggerRect.bottom)}px`;
            },
            openOnHover() {
                if (! this.prefersTap()) {
                    this.show();
                }
            },
            closeOnHover() {
                if (! this.prefersTap()) {
                    this.scheduleClose();
                }
            },
            close() {
                this.cancelClose();
                this.open = false;
            },
            focusTrigger(position) {
                const navigationMenu = this.$el.closest('[data-navigation-menu]');
                const triggers = Array.from(navigationMenu.querySelectorAll('[data-navigation-menu-trigger]'));
                const currentTrigger = this.$el.querySelector('[data-navigation-menu-trigger]');
                const currentIndex = triggers.indexOf(currentTrigger);
                const nextIndex = position === 'first'
                    ? 0
                    : position === 'last'
                        ? triggers.length - 1
                        : (currentIndex + position + triggers.length) % triggers.length;

                triggers[nextIndex]?.focus();
            },
            toggle() {
                if (this.open) {
                    this.close();

                    return;
                }

                this.show();
            },
        }"
        @mouseenter="openOnHover()"
        @mouseleave="closeOnHover()"
        @focusin="show()"
        @focusout="if (! $el.contains($event.relatedTarget)) scheduleClose()"
        @resize.window="if (open) positionMenu()"
        @keydown.escape.stop="close(); $el.querySelector('[data-navigation-menu-trigger]')?.focus()"
        @click.outside="close()"
        @nav-dropdown-open.window="if ($event.detail.id !== id) close()"
        data-history-navigation>
        <button type="button"
            class="group inline-flex h-8 w-max items-center justify-center gap-1.5 rounded-md bg-white px-2.5 py-2 text-sm leading-5 font-medium whitespace-nowrap text-gray-900 transition-[color,box-shadow] outline-none hover:bg-gray-100 hover:text-gray-900 focus:bg-gray-100 focus:text-gray-900 focus-visible:ring-2 focus-visible:ring-green-600/40 focus-visible:outline-1 data-[state=open]:bg-gray-100 data-[state=open]:text-gray-900 dark:bg-neutral-950 dark:text-gray-100 dark:hover:bg-neutral-800 dark:hover:text-gray-100 dark:focus:bg-neutral-800 dark:focus:text-gray-100 dark:data-[state=open]:bg-neutral-800 dark:data-[state=open]:text-gray-100 {{ $historyNavIsActive ? 'bg-gray-100 text-gray-900 dark:bg-neutral-800 dark:text-gray-100' : '' }}"
            @click="toggle()"
            @keydown.arrowdown.prevent="show(); $nextTick(() => $refs.menuContent.querySelector('a, button')?.focus())"
            @keydown.arrowright.prevent="focusTrigger(1)"
            @keydown.arrowleft.prevent="focusTrigger(-1)"
            @keydown.home.prevent="focusTrigger('first')"
            @keydown.end.prevent="focusTrigger('last')"
            :aria-expanded="open"
            :data-state="open ? 'open' : 'closed'"
            data-navigation-menu-trigger>
            History
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="relative top-px ml-1 size-3 flex-none text-neutral-400 transition duration-300 dark:text-neutral-500" :class="open ? 'rotate-180' : ''" aria-hidden="true">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M6 9l6 6l6 -6" />
            </svg>
        </button>

        <span x-show="open" x-cloak x-transition
            class="absolute left-1/2 top-full z-50 size-2 -translate-x-1/2 translate-y-1/2 rotate-45 bg-white ring-1 ring-gray-200 dark:bg-neutral-900 dark:ring-neutral-800"
            aria-hidden="true"></span>

        <div x-ref="menuContent"
            class="fixed left-1/2 top-16 z-50 mt-1.5 max-h-[calc(100vh-5rem)] w-[32rem] max-w-[calc(100vw-2rem)] origin-top overflow-y-auto overflow-x-hidden rounded-md border border-gray-200 bg-white p-2 pr-2.5 text-gray-900 shadow-lg shadow-black/5 -translate-x-1/2 dark:border-neutral-800 dark:bg-neutral-900 dark:text-gray-100 dark:shadow-black/20"
            x-show="open"
            x-cloak
            x-transition:enter="transform transition duration-200 ease-out"
            x-transition:enter-start="translate-y-1 scale-95 opacity-0"
            x-transition:enter-end="translate-y-0 scale-100 opacity-100"
            x-transition:leave="transform transition duration-150 ease-in"
            x-transition:leave-start="translate-y-0 scale-100 opacity-100"
            x-transition:leave-end="translate-y-1 scale-95 opacity-0"
            @mouseenter="cancelClose()"
            data-navigation-menu-content
            data-history-navigation-content
            :data-state="open ? 'open' : 'closed'">
            <div class="grid gap-1" data-history-navigation-seasons-grid>
                @forelse ($historySeasonGroups as $historySeasonGroup)
                    <div x-data="{ seasonKey: @js($historySeasonGroup['season']->getKey()) }" class="min-w-0" data-history-navigation-season>
                        <button type="button"
                            class="flex w-full items-center justify-between gap-3 rounded-sm px-2 py-2 text-left text-sm leading-5 font-medium text-gray-900 transition-colors hover:bg-gray-100 hover:text-gray-900 focus:bg-gray-100 focus:text-gray-900 focus:outline-none dark:text-gray-100 dark:hover:bg-neutral-800 dark:hover:text-gray-100 dark:focus:bg-neutral-800 dark:focus:text-gray-100"
                            @click="activeHistorySeason = activeHistorySeason === seasonKey ? null : seasonKey"
                            :aria-expanded="activeHistorySeason === seasonKey"
                            :class="activeHistorySeason === seasonKey ? 'bg-gray-100 text-gray-900 dark:bg-neutral-800 dark:text-gray-100' : ''"
                            data-history-navigation-season-trigger>
                            <span class="min-w-0 truncate">{{ $historySeasonGroup['season']->name }}</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3.5 shrink-0 text-gray-400 transition-transform dark:text-gray-500" :class="activeHistorySeason === seasonKey ? 'rotate-180' : ''" aria-hidden="true">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M6 9l6 6l6 -6" />
                            </svg>
                        </button>

                        <div x-show="activeHistorySeason === seasonKey" x-cloak class="mt-1 grid gap-0.5 sm:grid-cols-2" data-history-navigation-season-panel data-history-navigation-grid data-history-navigation-items>
                            @foreach ($historySeasonGroup['rulesets'] as $historyRulesetGroup)
                                @foreach ($historyRulesetGroup['sections'] as $historySection)
                                    <a href="{{ route('history.section.show', ['season' => $historySeasonGroup['season'], 'ruleset' => $historyRulesetGroup['ruleset'], 'section' => $historySection]) }}"
                                        class="block rounded-sm transition-colors hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                                        data-history-navigation-section-link data-history-navigation-item>
                                        <div class="rounded-sm px-1.5 py-1 text-sm leading-5 font-medium text-muted-foreground">
                                            <span>{{ $historySection->name }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            @endforeach

                            @if (($historySeasonGroup['knockouts'] ?? collect())->isNotEmpty())
                                @foreach ($historySeasonGroup['knockouts'] as $historyKnockout)
                                    <a href="{{ route('history.knockout.show', ['season' => $historySeasonGroup['season'], 'knockout' => $historyKnockout]) }}"
                                        class="block rounded-sm transition-colors hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                                        data-history-navigation-knockout-link data-history-navigation-item>
                                        <div class="rounded-sm px-1.5 py-1 text-sm leading-5 font-medium text-muted-foreground">
                                            <span>{{ $historyKnockout->name }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-md p-3 text-sm text-muted-foreground sm:col-span-2">Archived seasons will appear here once a season closes.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
