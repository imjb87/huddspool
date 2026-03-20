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
            <div class="absolute inset-0 overflow-y-auto px-4 py-4"
                x-show="activeDrawer === 'root'"
                x-cloak
                data-mobile-menu-panel="root"
                x-transition:enter="transform transition ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-1/4 opacity-0">
                <div class="{{ $mobileDrawerPanelContentClasses }}">
                    <div class="{{ $mobileDrawerListClasses }}">
                        @foreach ($navigationRulesets as $navigationRuleset)
                            @php
                                $ruleset = $navigationRuleset['ruleset'];
                            @endphp
                            <button type="button"
                                class="{{ $mobileDrawerLinkClasses }}"
                                data-mobile-ruleset-trigger
                                @click="openDrawer('ruleset-{{ $ruleset->id }}')">
                                <span>{{ $ruleset->name }}</span>
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        @endforeach
                    </div>

                    <div class="space-y-1 border-t border-gray-200 pt-3 dark:border-gray-800">
                        <button type="button"
                            class="{{ $mobileDrawerLinkClasses }}"
                            data-mobile-knockouts-trigger
                            @click="openDrawer('knockouts')">
                            <span>Knockouts</span>
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button type="button"
                            class="{{ $mobileDrawerLinkClasses }}"
                            data-mobile-history-trigger
                            @click="openDrawer('history')">
                            <span>History</span>
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <a href="{{ route('page.show', 'handbook') }}"
                            class="{{ $mobileDrawerTextLinkClasses }}">
                            Handbook
                        </a>
                    </div>

                    <div class="space-y-1 border-t border-gray-200 pt-3 dark:border-gray-800">
                        @if (@auth()->user())
                            <span class="block px-0 text-sm font-semibold leading-7 text-gray-500 dark:text-gray-400">{{ auth()->user()->name }}</span>
                            <a href="{{ route('account.show') }}"
                                class="{{ $mobileDrawerTextLinkClasses }}">
                                Account
                            </a>
                            @if (auth()->user()->is_admin)
                                <a href="{{ route('filament.admin.pages.dashboard') }}"
                                    class="{{ $mobileDrawerTextLinkClasses }}">
                                    Admin
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                    class="{{ $mobileDrawerTextLinkClasses }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    Log out
                                </a>
                            </form>
                        @else
                            <a href="{{ route('login') }}"
                                class="{{ $mobileDrawerTextLinkClasses }}">
                                Log in
                            </a>
                        @endif
                        <button type="button"
                            class="block w-full px-0 py-3 text-left text-base font-semibold leading-7 text-gray-900 transition hover:text-gray-700 dark:text-gray-100 dark:hover:text-gray-200"
                            x-cloak
                            x-show="canInstallApp"
                            @click="installApp()"
                            data-mobile-install-app-trigger>
                            Install app
                        </button>
                    </div>
                </div>
            </div>

            @foreach ($navigationRulesets as $navigationRuleset)
                @php
                    $ruleset = $navigationRuleset['ruleset'];
                    $navigableSections = $navigationRuleset['sections'];
                @endphp
                <div class="{{ $mobileDrawerPanelClasses }}"
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
                    <div class="{{ $mobileDrawerPanelContentClasses }}">
                        <button type="button"
                            class="{{ $mobileDrawerBackButtonClasses }}"
                            @click="goBackToRoot()">
                            <span class="{{ $mobileDrawerBackLabelClasses }}" data-mobile-back-label>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.78 4.97a.75.75 0 010 1.06L9.06 10l3.72 3.97a.75.75 0 11-1.1 1.02l-4.25-4.5a.75.75 0 010-1.04l4.25-4.5a.75.75 0 011.1-.02z" clip-rule="evenodd" />
                                </svg>
                                {{ $ruleset->name }}
                            </span>
                        </button>
                        <div class="{{ $mobileDrawerListClasses }}">
                            @foreach ($navigableSections as $section)
                                <a href="{{ route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section]) }}"
                                    class="{{ $mobileDrawerTextLinkClasses }}">
                                    {{ $section->name }}
                                </a>
                            @endforeach
                            <a href="{{ route('ruleset.show', $ruleset) }}"
                                class="{{ $mobileDrawerTextLinkClasses }}">
                                {{ $ruleset->name }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="{{ $mobileDrawerPanelClasses }}"
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
                <div class="{{ $mobileDrawerPanelContentClasses }}">
                    <button type="button"
                        class="{{ $mobileDrawerBackButtonClasses }}"
                        @click="goBackToRoot()">
                        <span class="{{ $mobileDrawerBackLabelClasses }}" data-mobile-back-label>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.78 4.97a.75.75 0 010 1.06L9.06 10l3.72 3.97a.75.75 0 11-1.1 1.02l-4.25-4.5a.75.75 0 010-1.04l4.25-4.5a.75.75 0 011.1-.02z" clip-rule="evenodd" />
                            </svg>
                            History
                        </span>
                    </button>
                    <div class="{{ $mobileDrawerListClasses }}">
                        @forelse ($historySeasonGroups as $historySeasonGroup)
                            <button type="button"
                                class="{{ $mobileDrawerLinkClasses }}"
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
                <div class="{{ $mobileDrawerPanelClasses }}"
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
                    <div class="{{ $mobileDrawerPanelContentClasses }}">
                        <button type="button"
                            class="{{ $mobileDrawerBackButtonClasses }}"
                            @click="openDrawer('history')">
                            <span class="{{ $mobileDrawerBackLabelClasses }}" data-mobile-back-label>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.78 4.97a.75.75 0 010 1.06L9.06 10l3.72 3.97a.75.75 0 11-1.1 1.02l-4.25-4.5a.75.75 0 010-1.04l4.25-4.5a.75.75 0 011.1-.02z" clip-rule="evenodd" />
                                </svg>
                                {{ $historySeasonGroup['season']->name }}
                            </span>
                        </button>
                        <div class="{{ $mobileDrawerListClasses }}">
                            @foreach ($historySeasonGroup['rulesets'] as $historyRulesetGroup)
                                <button type="button"
                                    class="{{ $mobileDrawerLinkClasses }}"
                                    data-mobile-history-ruleset-trigger
                                    @click="openDrawer('history-season-{{ $historySeasonGroup['season']->id }}-ruleset-{{ $historyRulesetGroup['ruleset']->id }}')">
                                    <span>{{ $historyRulesetGroup['ruleset']->name }}</span>
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @endforeach

                            @foreach ($historySeasonGroup['knockouts'] ?? [] as $historyKnockout)
                                <a href="{{ route('knockout.show', $historyKnockout) }}"
                                    class="{{ $mobileDrawerTextLinkClasses }}"
                                    data-mobile-history-knockout-link>
                                    {{ $historyKnockout->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                @foreach ($historySeasonGroup['rulesets'] as $historyRulesetGroup)
                    <div class="{{ $mobileDrawerPanelClasses }}"
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
                        <div class="{{ $mobileDrawerPanelContentClasses }}">
                            <button type="button"
                                class="{{ $mobileDrawerBackButtonClasses }}"
                                @click="openDrawer('history-season-{{ $historySeasonGroup['season']->id }}')">
                                <span class="{{ $mobileDrawerBackLabelClasses }}" data-mobile-back-label>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.78 4.97a.75.75 0 010 1.06L9.06 10l3.72 3.97a.75.75 0 11-1.1 1.02l-4.25-4.5a.75.75 0 010-1.04l4.25-4.5a.75.75 0 011.1-.02z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $historyRulesetGroup['ruleset']->name }}
                                </span>
                            </button>
                            <div class="{{ $mobileDrawerListClasses }}">
                                @foreach ($historyRulesetGroup['sections'] as $historySection)
                                    <a href="{{ route('history.section.show', ['season' => $historySeasonGroup['season'], 'ruleset' => $historyRulesetGroup['ruleset'], 'section' => $historySection]) }}"
                                        class="{{ $mobileDrawerTextLinkClasses }}">
                                        {{ $historySection->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach

            <div class="{{ $mobileDrawerPanelClasses }}"
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
                <div class="{{ $mobileDrawerPanelContentClasses }}">
                    <button type="button"
                        class="{{ $mobileDrawerBackButtonClasses }}"
                        @click="goBackToRoot()">
                        <span class="{{ $mobileDrawerBackLabelClasses }}" data-mobile-back-label>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.78 4.97a.75.75 0 010 1.06L9.06 10l3.72 3.97a.75.75 0 11-1.1 1.02l-4.25-4.5a.75.75 0 010-1.04l4.25-4.5a.75.75 0 011.1-.02z" clip-rule="evenodd" />
                            </svg>
                            Knockouts
                        </span>
                    </button>
                    <div class="{{ $mobileDrawerListClasses }}">
                        @foreach ($navigableKnockouts as $knockout)
                            <a href="{{ route('knockout.show', $knockout) }}"
                                class="{{ $mobileDrawerTextLinkClasses }}">
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
