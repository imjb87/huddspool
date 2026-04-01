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
    <div class="space-y-4">
        @if (@auth()->user())
            <div class="ui-card overflow-hidden">
                @include('layouts.partials.theme-switcher', ['mobile' => true, 'grouped' => true])

                <div class="border-t border-gray-200 dark:border-zinc-700/75"></div>

                <a href="{{ route('account.show') }}"
                    class="ui-card-row-link">
                    <div class="ui-card-row justify-start gap-3 px-4 sm:px-5">
                        <img
                            src="{{ auth()->user()->avatar_url }}"
                            alt="{{ auth()->user()->name }} avatar"
                            class="h-9 w-9 rounded-full object-cover"
                        >
                        <span class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-100">{{ auth()->user()->name }}</span>
                    </div>
                </a>
            </div>
        @else
            <div class="ui-card overflow-hidden">
                @include('layouts.partials.theme-switcher', ['mobile' => true, 'grouped' => true])

                <div class="border-t border-gray-200 dark:border-zinc-700/75"></div>

                <a href="{{ route('login') }}"
                    class="ui-card-row-link">
                    <div class="ui-card-row px-4 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 sm:px-5">
                        Log in
                    </div>
                </a>
            </div>
        @endif

        <div class="ui-card overflow-hidden">
            <div class="ui-card-rows">
                @foreach ($navigationRulesets as $navigationRuleset)
                    <button type="button"
                        class="ui-card-row w-full cursor-pointer items-center justify-between px-4 text-left text-base font-semibold leading-7 text-gray-900 transition hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-zinc-700/85 sm:px-5"
                        data-mobile-ruleset-trigger
                        @click="openDrawer('ruleset-{{ $navigationRuleset['id'] }}')">
                        <span>{{ $navigationRuleset['name'] }}</span>
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="ui-card overflow-hidden">
            <div class="ui-card-rows">
                <button type="button"
                    class="ui-card-row w-full cursor-pointer items-center justify-between px-4 text-left text-base font-semibold leading-7 text-gray-900 transition hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-zinc-700/85 sm:px-5"
                    data-mobile-knockouts-trigger
                    @click="openDrawer('knockouts')">
                    <span>Knockouts</span>
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
                <button type="button"
                    class="ui-card-row w-full cursor-pointer items-center justify-between px-4 text-left text-base font-semibold leading-7 text-gray-900 transition hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-zinc-700/85 sm:px-5"
                    data-mobile-history-trigger
                    @click="openDrawer('history')">
                    <span>History</span>
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
                <a href="{{ route('page.show', 'handbook') }}"
                    class="ui-card-row-link">
                    <div class="ui-card-row px-4 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 sm:px-5">
                        Handbook
                    </div>
                </a>
            </div>
        </div>

        <div class="ui-card overflow-hidden"
            @unless (auth()->check())
                x-cloak
                x-show="canInstallApp"
            @endunless
        >
            <div class="ui-card-rows">
            @if (@auth()->user())
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('filament.admin.pages.dashboard') }}"
                        class="ui-card-row-link">
                        <div class="ui-card-row px-4 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 sm:px-5">
                            Admin
                        </div>
                    </a>
                @endif
                <button type="button"
                    class="ui-card-row w-full cursor-pointer px-4 text-left text-base font-semibold leading-7 text-gray-900 transition hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-zinc-700/85 sm:px-5"
                    x-cloak
                    x-show="canInstallApp"
                    @click="installApp()"
                    data-mobile-install-app-trigger>
                    Install app
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                        class="ui-card-row-link"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        <div class="ui-card-row px-4 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 sm:px-5">
                            Log out
                        </div>
                    </a>
                </form>
            @else
                <button type="button"
                    class="ui-card-row w-full cursor-pointer px-4 text-left text-base font-semibold leading-7 text-gray-900 transition hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-zinc-700/85 sm:px-5"
                    x-cloak
                    x-show="canInstallApp"
                    @click="installApp()"
                    data-mobile-install-app-trigger>
                    Install app
                </button>
            @endif
            </div>
        </div>
    </div>
</div>
