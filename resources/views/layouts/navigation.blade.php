@php
    $currentRuleset = request()->route('ruleset');
    $isRulesetRoute = request()->routeIs('ruleset.show', 'ruleset.section.show', 'table.index', 'fixture.index', 'player.index');
@endphp

<header class="site-header fixed top-0 z-50 w-full bg-white transition-all duration-500"
    x-data="{ open: false, scroll: false }"
    @scroll.window="scroll = (window.pageYOffset > 0) ? true : false"
    :class="{ 'shadow-lg': scroll || open }">
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
                        class="flex items-center gap-x-1 text-sm font-semibold leading-6 {{ $isActiveRuleset ? 'text-green-700' : 'text-gray-900 hover:text-green-700' }}"
                        @click="open = ! open" :aria-expanded="open">
                        {{ $ruleset->name }}
                        <svg class="h-4 w-4 flex-none text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div class="absolute left-0 top-full z-10 mt-3 w-72 rounded-2xl bg-white p-2 shadow-lg ring-1 ring-gray-900/5"
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
                                class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-50">
                                {{ $section->name }}
                            </a>
                        @endforeach
                        <div class="mx-2 my-1 border-t border-gray-200"></div>
                        <a href="{{ route('ruleset.show', $ruleset) }}"
                            class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-50">
                            Ruleset
                        </a>
                    </div>
                </div>
            @endforeach

            <a href="{{ route('knockout.index') }}"
                class="text-sm font-semibold leading-6 {{ request()->routeIs('knockout.*') ? 'text-green-700' : 'text-gray-900 hover:text-green-700' }}">
                Knockouts
            </a>
            <a href="{{ route('history.index') }}"
                class="text-sm font-semibold leading-6 {{ request()->routeIs('history.*') ? 'text-green-700' : 'text-gray-900 hover:text-green-700' }}">
                History
            </a>
            <a href="{{ route('page.show', 'handbook') }}"
                class="text-sm font-semibold leading-6 {{ request()->routeIs('page.show') && request()->route('page') === 'handbook' ? 'text-green-700' : 'text-gray-900 hover:text-green-700' }}">
                Handbook
            </a>
        </div>

        <div class="flex items-center gap-x-4 lg:hidden">
            <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
                data-site-search-trigger aria-label="Open search">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
                @click="open = !open" :aria-expanded="open">
                <span class="sr-only">Open main menu</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>

        <div class="hidden lg:flex lg:flex-1 lg:justify-end lg:gap-x-6">
            <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
                data-site-search-trigger aria-label="Open search">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            @if (@auth()->user())
                <div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
                    <button type="button"
                        class="flex items-center gap-x-1 text-sm font-semibold leading-6 text-gray-900"
                        aria-expanded="false" @click="open = !open" :aria-expanded="open">
                        {{ auth()->user()->name }}
                        <svg class="h-5 w-5 flex-none text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="absolute right-0 top-full z-10 mt-3 w-56 rounded-xl bg-white p-2 shadow-lg ring-1 ring-gray-900/5"
                        x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        @click="open = false">
                        <div class="py-1">
                            <a href="{{ route('player.show', auth()->user()) }}"
                                class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">
                                Your profile
                            </a>
                        </div>
                        @if (auth()->user()->team_id)
                            <div class="py-1">
                                <a href="{{ route('team.show', auth()->user()->team_id) }}"
                                    class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">
                                    Your team
                                </a>
                            </div>
                        @endif
                        <div class="py-1">
                            <a href="{{ route('support.tickets') }}"
                                class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">
                                Submit a request
                            </a>
                        </div>
                        @if (auth()->user()->is_admin)
                            <div class="py-1">
                                <a href="{{ route('filament.admin.pages.dashboard') }}"
                                    class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">
                                    Admin
                                </a>
                            </div>
                        @endif
                        @if (app('impersonate')->isImpersonating())
                            <div class="py-1">
                                <a class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50"
                                    href="{{ route('filament-impersonate.leave') }}">
                                    Stop impersonating
                                </a>
                            </div>
                        @endif
                        <div class="py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                    class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    Log out
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="text-sm font-semibold leading-6 text-gray-900">
                    Log in <span aria-hidden="true">&rarr;</span>
                </a>
            @endif
        </div>
    </nav>

    <div class="lg:hidden relative z-50" role="dialog" aria-modal="true" @click.away="open = false"
        @close.stop="open = false" @keydown.escape="open = false" x-cloak x-show="open">
        <div class="fixed inset-0 z-20 bg-gray-500/50 transition-opacity" x-show="open"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div class="fixed inset-y-0 right-0 z-30 w-full overflow-y-auto px-4 py-3 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10"
            x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1">
            <div class="mx-auto max-w-xl overflow-hidden rounded-xl bg-white px-6 py-4 shadow-2xl ring-1 ring-black/5 transition-all">
                <div class="space-y-6">
                    <div class="space-y-3">
                        @foreach ($rulesets as $ruleset)
                            @php
                                $navigableSections = $ruleset->openSections
                                    ->filter(fn ($section) => filled($section?->getRouteKey()))
                                    ->values();
                            @endphp
                            @continue($navigableSections->isEmpty())
                            <div x-data="{ sectionsOpen: {{ $currentRuleset instanceof \App\Models\Ruleset && $currentRuleset->is($ruleset) ? 'true' : 'false' }} }"
                                data-mobile-ruleset-group>
                                <button type="button"
                                    class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-left text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50"
                                    data-mobile-ruleset-trigger
                                    @click="sectionsOpen = ! sectionsOpen" :aria-expanded="sectionsOpen">
                                    <span>{{ $ruleset->name }}</span>
                                    <svg class="h-5 w-5 text-gray-400 transition-transform" :class="{ 'rotate-180': sectionsOpen }"
                                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div class="mt-1 space-y-1 pl-3" x-show="sectionsOpen" x-cloak data-mobile-ruleset-sections>
                                    @foreach ($navigableSections as $section)
                                        <a href="{{ route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section]) }}"
                                            class="block rounded-lg px-3 py-3 text-sm font-semibold leading-6 text-gray-700 hover:bg-gray-50">
                                            {{ $section->name }}
                                        </a>
                                    @endforeach
                                    <a href="{{ route('ruleset.show', $ruleset) }}"
                                        class="block rounded-lg px-3 py-3 text-sm font-semibold leading-6 text-gray-700 hover:bg-gray-50">
                                        Ruleset
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-2 border-t border-gray-200 pt-4">
                        <a href="{{ route('knockout.index') }}"
                            class="block rounded-lg px-3 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">
                            Knockouts
                        </a>
                        <a href="{{ route('history.index') }}"
                            class="block rounded-lg px-3 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">
                            History
                        </a>
                        <a href="{{ route('page.show', 'handbook') }}"
                            class="block rounded-lg px-3 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">
                            Handbook
                        </a>
                    </div>

                    <div class="space-y-2 border-t border-gray-200 pt-4">
                        @if (@auth()->user())
                            <span class="block px-3 text-sm font-semibold leading-7 text-gray-500">{{ auth()->user()->name }}</span>
                            <a href="{{ route('player.show', auth()->user()) }}"
                                class="block rounded-lg px-3 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">
                                Your profile
                            </a>
                            @if (auth()->user()->team_id)
                                <a href="{{ route('team.show', auth()->user()->team_id) }}"
                                    class="block rounded-lg px-3 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">
                                    Your team
                                </a>
                            @endif
                            <a href="{{ route('support.tickets') }}"
                                class="block rounded-lg px-3 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">
                                Submit a request
                            </a>
                            @if (auth()->user()->is_admin)
                                <a href="{{ route('filament.admin.pages.dashboard') }}"
                                    class="block rounded-lg px-3 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">
                                    Admin
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                    class="block rounded-lg px-3 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    Log out
                                </a>
                            </form>
                        @else
                            <a href="{{ route('login') }}"
                                class="block rounded-lg px-3 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">
                                Log in
                            </a>
                        @endif
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
