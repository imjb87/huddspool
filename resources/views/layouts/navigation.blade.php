<header class="bg-white fixed top-0 w-full z-50 duration-500 transition-all" x-data="{ open: false, scroll: false }"
    @scroll.window="scroll = (window.pageYOffset > 0) ? true : false" :class="{ 'shadow-lg': scroll || open }">
    <nav class="mx-auto flex max-w-7xl items-center justify-between py-5 px-4 lg:px-8" aria-label="Global">
        <div class="flex lg:flex-1">
            <a href="#" class="-m-1.5 p-1.5">
                <span class="sr-only">Huddersfield & District Tuesday Night Pool League</span>
                <x-application-logo />
            </a>
        </div>
        <div class="flex lg:hidden gap-x-4">
            <button class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
                id="searchIcon">
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
        <div class="hidden lg:flex lg:gap-x-8">
            <div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
                <button type="button" class="flex items-center gap-x-1 text-sm font-semibold leading-6 text-gray-900"
                    aria-expanded="false" @click="open = !open" :aria-expanded="open">
                    Tables
                    <svg class="h-5 w-5 flex-none text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div class="absolute -left-8 top-full z-10 mt-3 w-56 rounded-xl bg-white p-2 shadow-lg ring-1 ring-gray-900/5"
                    x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1" @click="open = false">
                    @foreach ($rulesets as $ruleset)
                        <div class="py-1">
                            <a href="{{ route('table.index', $ruleset->slug) }}"
                                class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">{{ $ruleset->name }}</a>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
                <button type="button" class="flex items-center gap-x-1 text-sm font-semibold leading-6 text-gray-900"
                    aria-expanded="false" @click="open = !open" :aria-expanded="open">
                    Fixtures &amp; Results
                    <svg class="h-5 w-5 flex-none text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div class="absolute -left-8 top-full z-10 mt-3 w-56 rounded-xl bg-white p-2 shadow-lg ring-1 ring-gray-900/5"
                    x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1" @click="open = false">
                    @foreach ($rulesets as $ruleset)
                        <div class="py-1">
                            <a href="{{ route('fixture.index', $ruleset->slug) }}"
                                class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">{{ $ruleset->name }}</a>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
                <button type="button" class="flex items-center gap-x-1 text-sm font-semibold leading-6 text-gray-900"
                    aria-expanded="false" @click="open = !open" :aria-expanded="open">
                    Averages
                    <svg class="h-5 w-5 flex-none text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div class="absolute -left-8 top-full z-10 mt-3 w-56 rounded-xl bg-white p-2 shadow-lg ring-1 ring-gray-900/5"
                    x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1" @click="open = false">
                    @foreach ($rulesets as $ruleset)
                        <div class="py-1">
                            <a href="{{ route('player.index', $ruleset->slug) }}"
                                class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">{{ $ruleset->name }}</a>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
                <button type="button" class="flex items-center gap-x-1 text-sm font-semibold leading-6 text-gray-900"
                    aria-expanded="false" @click="open = !open" :aria-expanded="open">
                    History
                    <svg class="h-5 w-5 flex-none text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div class="absolute -left-8 top-full z-10 mt-3 w-64 rounded-xl bg-white p-3 shadow-lg ring-1 ring-gray-900/5"
                    x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1" @click="open = false">
                    @foreach ($past_seasons as $season)
                        @php
                            $seasonRulesets = $season->sections->map(fn($section) => $section->ruleset)->filter()->unique('id')->values();
                        @endphp
                        @if ($seasonRulesets->isNotEmpty())
                            <div class="py-2">
                                <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $season->name }}</p>
                                @foreach ($seasonRulesets as $ruleset)
                                    <a href="{{ route('history.show', [$season, $ruleset]) }}"
                                        class="block rounded-md py-2 pl-6 pr-4 text-sm leading-5 text-gray-700 hover:bg-gray-50 font-semibold">{{ $ruleset->name }}</a>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
                <button type="button" class="flex items-center gap-x-1 text-sm font-semibold leading-6 text-gray-900"
                    aria-expanded="false" @click="open = !open" :aria-expanded="open">
                    Knockouts
                    <svg class="h-5 w-5 flex-none text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div class="absolute -left-8 top-full z-10 mt-3 w-56 rounded-xl bg-white p-2 shadow-lg ring-1 ring-gray-900/5"
                    x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1" @click="open = false">
                    <div class="py-1">
                        <a href="{{ asset('knockouts/KOSCHEDULE.pdf') }}" target="_blank"
                            class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">Knockout
                            Schedule</a>
                    </div>
                    <div class="py-1">
                        <a href="{{ asset('knockouts/BBINTSinglesKO.pdf') }}" target="_blank"
                            class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">BB/Int
                            Singles Knockout</a>
                    </div>
                    <div class="py-1">
                        <a href="{{ asset('knockouts/BBTeamKO.pdf') }}" target="_blank"
                            class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">BB/Int
                            Team Knockout</a>
                    </div>
                    <div class="py-1">
                        <a href="{{ asset('knockouts/DoublesKO.pdf') }}" target="_blank"
                            class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">Doubles
                            Knockout</a>
                    </div>
                    <div class="py-1">
                        <a href="{{ asset('knockouts/EPASinglesKO.pdf') }}" target="_blank"
                            class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">EPA
                            Singles Knockout</a>
                    </div>
                    <div class="py-1">
                        <a href="{{ asset('knockouts/EPATeamKO.pdf') }}" target="_blank"
                            class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">EPA
                            Team Knockout</a>
                    </div>
                    <div class="py-1">
                        <a href="{{ asset('knockouts/MixedDoublesKO.pdf') }}" target="_blank"
                            class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">Mixed
                            Doubles Knockout</a>
                    </div>
                </div>
            </div>
            <div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
                <button type="button" class="flex items-center gap-x-1 text-sm font-semibold leading-6 text-gray-900"
                    aria-expanded="false" @click="open = !open" :aria-expanded="open">
                    Official
                    <svg class="h-5 w-5 flex-none text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div class="absolute -left-8 top-full z-10 mt-3 w-56 rounded-xl bg-white p-2 shadow-lg ring-1 ring-gray-900/5"
                    x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1" @click="open = false">
                    @foreach ($rulesets as $ruleset)
                        <div class="py-1">
                            <a href="{{ route('ruleset.show', $ruleset->slug) }}"
                                class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">{{ $ruleset->name }}</a>
                        </div>
                    @endforeach
                    <div class="py-1">
                        <a href="{{ route('page.show', 'handbook') }}"
                            class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">Handbook</a>
                    </div>
                </div>
            </div>      
        </div>
        <div class="hidden lg:flex lg:flex-1 lg:justify-end lg:gap-x-6">
            <button class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
                id="searchIcon">
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
                        x-transition:leave-end="opacity-0 translate-y-1" @click="open = false">
                        <div class="py-1">
                            <a href="{{ route('player.show', auth()->user()->id) }}"
                                class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">Your profile</a>
                        </div>
                        @if (auth()->user()->team)
                            <div class="py-1">
                                <a href="{{ route('team.show', auth()->user()->team->id) }}"
                                    class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">Your
                                    team</a>
                            </div>
                        @endif
                        @if (auth()->user()->is_admin)
                            <div class="py-1">
                                <a href="{{ route('filament.admin.pages.dashboard') }}"
                                    class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50">Admin</a>
                            </div>
                        @endif
                        @if (app('impersonate')->isImpersonating())
                            <div class="py-1">
                                <a class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50" href="{{ route('filament-impersonate.leave') }}">Stop impersonating</a>
                            </div>
                        @endif
                        
                        <div class="py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                    class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50"
                                    onclick="event.preventDefault(); this.closest('form').submit();">Log out</a>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="text-sm font-semibold leading-6 text-gray-900">Log in <span
                        aria-hidden="true">&rarr;</span></a>
            @endif
        </div>
    </nav>
    <!-- Mobile menu, show/hide based on menu open state. -->
    <div class="lg:hidden relative z-[99]" role="dialog" aria-modal="true" @click.away="open = false"
        @close.stop="open = false" @keydown.escape="open = false" x-cloak x-show="open">
        <!-- Background backdrop, show/hide based on slide-over state. -->
        <div class="fixed inset-0 z-20 bg-gray-500 bg-opacity-50 transition-opacity" x-show="open"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div class="fixed inset-y-0 right-0 z-30 w-full overflow-y-auto px-4 py-[12px] sm:max-w-sm sm:ring-1 sm:ring-gray-900/10"
            x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1">
            <div class="mx-auto max-w-xl transform overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all px-6 py-4"
                @click.away="open = false">
                <div class="flow-root">
                    <div class="-my-4 divide-y divide-gray-500/10">
                        <div class="space-y-2 py-4">
                            <div class="-mx-3" x-data="{ open: false }">
                                <button type="button"
                                    class="flex w-full items-center justify-between rounded-lg py-2 pl-3 pr-3.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50"
                                    aria-controls="disclosure-1" aria-expanded="false" @click="open = !open"
                                    :aria-expanded="open">
                                    Tables
                                    <!--
                  Expand/collapse icon, toggle classes based on menu open state.

                  Open: "rotate-180", Closed: ""
                -->
                                    <svg class="h-5 w-5 flex-none" viewBox="0 0 20 20" fill="currentColor"
                                        aria-hidden="true" :class="{ 'rotate-180': open }">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <!-- 'Product' sub-menu, show/hide based on menu state. -->
                                <div class="mt-2 space-y-2" id="disclosure-1" x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                                    @foreach ($rulesets as $ruleset)
                                        <a href="{{ route('table.index', $ruleset->slug) }}"
                                            class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">{{ $ruleset->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="-mx-3" x-data="{ open: false }">
                                <button type="button"
                                    class="flex w-full items-center justify-between rounded-lg py-2 pl-3 pr-3.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50"
                                    aria-controls="disclosure-1" aria-expanded="false" @click="open = !open"
                                    :aria-expanded="open">
                                    Fixtures &amp; Results
                                    <!--
                  Expand/collapse icon, toggle classes based on menu open state.

                  Open: "rotate-180", Closed: ""
                -->
                                    <svg class="h-5 w-5 flex-none" viewBox="0 0 20 20" fill="currentColor"
                                        aria-hidden="true" :class="{ 'rotate-180': open }">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <!-- 'Product' sub-menu, show/hide based on menu state. -->
                                <div class="mt-2 space-y-2" id="disclosure-1" x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                                    @foreach ($rulesets as $ruleset)
                                        <a href="{{ route('fixture.index', $ruleset->slug) }}"
                                            class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">{{ $ruleset->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="-mx-3" x-data="{ open: false }">
                                <button type="button"
                                    class="flex w-full items-center justify-between rounded-lg py-2 pl-3 pr-3.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50"
                                    aria-controls="disclosure-1" aria-expanded="false" @click="open = !open"
                                    :aria-expanded="open">
                                    Averages
                                    <!--
                  Expand/collapse icon, toggle classes based on menu open state.

                  Open: "rotate-180", Closed: ""
                -->
                                    <svg class="h-5 w-5 flex-none" viewBox="0 0 20 20" fill="currentColor"
                                        aria-hidden="true" :class="{ 'rotate-180': open }">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <!-- 'Product' sub-menu, show/hide based on menu state. -->
                                <div class="mt-2 space-y-2" id="disclosure-1" x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                                    @foreach ($rulesets as $ruleset)
                                        <a href="{{ route('player.index', $ruleset->slug) }}"
                                            class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">{{ $ruleset->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="-mx-3" x-data="{ open: false }">
                                <button type="button"
                                    class="flex w-full items-center justify-between rounded-lg py-2 pl-3 pr-3.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50"
                                    aria-controls="disclosure-history" aria-expanded="false" @click="open = !open"
                                    :aria-expanded="open">
                                    History
                                    <svg class="h-5 w-5 flex-none" viewBox="0 0 20 20" fill="currentColor"
                                        aria-hidden="true" :class="{ 'rotate-180': open }">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div class="mt-2 space-y-2" id="disclosure-history" x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                                    @foreach ($past_seasons as $season)
                                        @php
                                            $seasonRulesets = $season->sections->map(fn ($section) => $section->ruleset)->filter()->unique('id')->values();
                                        @endphp
                                        @if ($seasonRulesets->isNotEmpty())
                                            <div class="space-y-1">
                                                <p class="px-6 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                    {{ $season->name }}
                                                </p>
                                                @foreach ($seasonRulesets as $ruleset)
                                                    <a href="{{ route('history.show', [$season, $ruleset]) }}"
                                                        class="block rounded-lg py-2 pl-9 pr-3 text-sm leading-7 text-gray-900 hover:bg-gray-50 font-semibold">
                                                        {{ $ruleset->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="-mx-3" x-data="{ open: false }">
                                <button type="button"
                                    class="flex w-full items-center justify-between rounded-lg py-2 pl-3 pr-3.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50"
                                    aria-controls="disclosure-1" aria-expanded="false" @click="open = !open"
                                    :aria-expanded="open">
                                    Knockouts
                                    <!--
                  Expand/collapse icon, toggle classes based on menu open state.

                  Open: "rotate-180", Closed: ""
                -->
                                    <svg class="h-5 w-5 flex-none" viewBox="0 0 20 20" fill="currentColor"
                                        aria-hidden="true" :class="{ 'rotate-180': open }">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <!-- 'Product' sub-menu, show/hide based on menu state. -->
                                <div class="mt-2 space-y-2" id="disclosure-1" x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                                    <a href="{{ asset('knockouts/KOSCHEDULE.pdf') }}" target="_blank"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">Knockout
                                        Schedule</a>
                                    <a href="{{ asset('knockouts/BBINTSinglesKO.pdf') }}" target="_blank"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">BB/Int
                                        Singles Knockout</a>
                                    <a href="{{ asset('knockouts/BBTeamKO.pdf') }}" target="_blank"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">BB/Int
                                        Team Knockout</a>
                                    <a href="{{ asset('knockouts/DoublesKO.pdf') }}" target="_blank"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">Doubles
                                        Knockout</a>
                                    <a href="{{ asset('knockouts/EPASinglesKO.pdf') }}" target="_blank"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">EPA
                                        Singles Knockout</a>
                                    <a href="{{ asset('knockouts/EPATeamKO.pdf') }}" target="_blank"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">EPA
                                        Team Knockout</a>
                                    <a href="{{ asset('knockouts/MixedDoublesKO.pdf') }}" target="_blank"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">Mixed
                                        Doubles Knockout</a>
                                </div>
                            </div>
                            <div class="-mx-3" x-data="{ open: false }">
                                <button type="button"
                                    class="flex w-full items-center justify-between rounded-lg py-2 pl-3 pr-3.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50"
                                    aria-controls="disclosure-1" aria-expanded="false" @click="open = !open"
                                    :aria-expanded="open">
                                    Official
                                    <!--
                  Expand/collapse icon, toggle classes based on menu open state.

                  Open: "rotate-180", Closed: ""
                -->
                                    <svg class="h-5 w-5 flex-none" viewBox="0 0 20 20" fill="currentColor"
                                        aria-hidden="true" :class="{ 'rotate-180': open }">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <!-- 'Product' sub-menu, show/hide based on menu state. -->
                                <div class="mt-2 space-y-2" id="disclosure-1" x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                                    @foreach ($rulesets as $ruleset)
                                        <a href="{{ route('ruleset.show', $ruleset->slug) }}"
                                            class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">{{ $ruleset->name }}</a>
                                    @endforeach
                                    <a href="{{ route('page.show', 'handbook') }}"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">Handbook</a>
                                </div>
                            </div>                         
                        </div>
                        <div class="py-4">
                            @if (@auth()->user())
                                <span
                                    class="block rounded-lg py-1 -mx-3 px-3 text-sm font-semibold leading-7 text-gray-500 hover:bg-gray-50">{{ auth()->user()->name }}</span>
                                <a href="{{ route('player.show', auth()->user()->id) }}"
                                    class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Your
                                    profile</a> 
                                @if (auth()->user()->team)
                                    <a href="{{ route('team.show', auth()->user()->team->id) }}"
                                        class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Your
                                        team</a>
                                @endif
                                @if (auth()->user()->is_admin)
                                    <a href="{{ route('filament.admin.pages.dashboard') }}"
                                        class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Admin</a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="route('logout')"
                                        class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50"
                                        onclick="event.preventDefault();
                                  this.closest('form').submit();">
                                        Log out
                                    </a>
                                </form>
                            @else
                                <a class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50"
                                    href="{{ route('login') }}">Log in</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('#searchIcon').forEach(item => {
            item.addEventListener('click', event => {
                Livewire.dispatch('openSearch'); // Emit the event to the Livewire component
                setTimeout(function() {
                    document.getElementById('searchInput').focus();
                }, 300);
            })
        })
    </script>
</header>
