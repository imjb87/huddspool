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
                <button type="button"
                    class="{{ $mobileDrawerLinkClasses }}"
                    data-mobile-ruleset-trigger
                    @click="openDrawer('ruleset-{{ $navigationRuleset['id'] }}')">
                    <span>{{ $navigationRuleset['name'] }}</span>
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
                @if (auth()->user()->isAdmin())
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
