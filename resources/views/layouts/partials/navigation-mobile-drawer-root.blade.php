<div class="mobile-menu-panel absolute inset-0 overflow-y-auto px-4 py-4"
    x-show="activeDrawer === 'root'"
    x-cloak
    data-mobile-menu-panel="root"
    :class="mobileMenuPanelClasses('root')"
    x-transition:enter="ui-motion-panel-in"
    x-transition:enter-start="ui-motion-panel-enter-start"
    x-transition:enter-end="ui-motion-panel-enter-end"
    x-transition:leave="ui-motion-panel-out"
    x-transition:leave-start="ui-motion-panel-leave-start"
    x-transition:leave-end="ui-motion-panel-leave-end">
    <div class="space-y-3">
        @if (@auth()->user())
            <div class="ui-card">
                <div class="ui-card-rows">
                    <a href="{{ route('account.show') }}"
                        class="ui-card-row-link">
                        <div class="ui-card-row items-center justify-start gap-3">
                            <img src="{{ auth()->user()->avatar_url }}"
                                alt="{{ auth()->user()->name }} avatar"
                                class="size-8 shrink-0 rounded-full object-cover ring-1 ring-foreground/10">
                            <span class="min-w-0 truncate font-medium">{{ auth()->user()->name }}</span>
                        </div>
                    </a>
                </div>
            </div>
        @else
            <div class="ui-card">
                <div class="ui-card-rows">
                    <a href="{{ route('login') }}"
                        class="ui-card-row-link">
                        <div class="ui-card-row justify-start">
                            Log in
                        </div>
                    </a>
                </div>
            </div>
        @endif

        <div class="ui-card">
            <div class="ui-card-rows">
                @foreach ($navigationRulesets as $navigationRuleset)
                    <button type="button"
                        class="ui-card-row w-full cursor-pointer items-center justify-between text-left transition-colors"
                        data-mobile-ruleset-trigger
                        @click="openDrawer('ruleset-{{ $navigationRuleset['id'] }}')">
                        <span class="min-w-0 truncate">{{ $navigationRuleset['name'] }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0 text-gray-400 dark:text-gray-500" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 6l6 6l-6 6" />
                        </svg>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="ui-card">
            <div class="ui-card-rows">
                <button type="button"
                    class="ui-card-row w-full cursor-pointer items-center justify-between text-left transition-colors"
                    data-mobile-knockouts-trigger
                    @click="openDrawer('knockouts')">
                    <span>Knockouts</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0 text-gray-400 dark:text-gray-500" aria-hidden="true">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M9 6l6 6l-6 6" />
                    </svg>
                </button>
                <button type="button"
                    class="ui-card-row w-full cursor-pointer items-center justify-between text-left transition-colors"
                    data-mobile-official-trigger
                    @click="openDrawer('official')">
                    <span>Official</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0 text-gray-400 dark:text-gray-500" aria-hidden="true">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M9 6l6 6l-6 6" />
                    </svg>
                </button>
                <button type="button"
                    class="ui-card-row w-full cursor-pointer items-center justify-between text-left transition-colors"
                    data-mobile-history-trigger
                    @click="openDrawer('history')">
                    <span>History</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0 text-gray-400 dark:text-gray-500" aria-hidden="true">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M9 6l6 6l-6 6" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="ui-card"
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
                        <div class="ui-card-row justify-start">
                            Admin
                        </div>
                    </a>
                @endif
                <button type="button"
                    class="ui-card-row w-full cursor-pointer justify-start text-left transition-colors"
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
                        <div class="ui-card-row justify-start">
                            Log out
                        </div>
                    </a>
                </form>
            @else
                <button type="button"
                    class="ui-card-row w-full cursor-pointer justify-start text-left transition-colors"
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
