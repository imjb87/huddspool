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

                <div class="border-t border-gray-200 dark:border-neutral-800/75"></div>

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

                <div class="border-t border-gray-200 dark:border-neutral-800/75"></div>

                <a href="{{ route('login') }}"
                    class="ui-card-row-link">
                    <div class="ui-card-row justify-start gap-3 px-4 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 sm:px-5">
                        <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-7.5a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 6 21h7.5a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                        </span>
                        <span>Log in</span>
                    </div>
                </a>
            </div>
        @endif

        <div class="ui-card overflow-hidden">
            <div class="ui-card-rows">
                @foreach ($navigationRulesets as $navigationRuleset)
                    <button type="button"
                        class="ui-card-row w-full cursor-pointer items-center justify-between px-4 text-left text-base font-semibold leading-7 text-gray-900 transition hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-neutral-800/85 sm:px-5"
                        data-mobile-ruleset-trigger
                        @click="openDrawer('ruleset-{{ $navigationRuleset['id'] }}')">
                        <span class="flex min-w-0 items-center gap-3">
                            <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                                </svg>
                            </span>
                            <span class="truncate">{{ $navigationRuleset['name'] }}</span>
                        </span>
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
                    class="ui-card-row w-full cursor-pointer items-center justify-between px-4 text-left text-base font-semibold leading-7 text-gray-900 transition hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-neutral-800/85 sm:px-5"
                    data-mobile-knockouts-trigger
                    @click="openDrawer('knockouts')">
                    <span class="flex min-w-0 items-center gap-3">
                        <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                            </svg>
                        </span>
                        <span>Knockouts</span>
                    </span>
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
                <button type="button"
                    class="ui-card-row w-full cursor-pointer items-center justify-between px-4 text-left text-base font-semibold leading-7 text-gray-900 transition hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-neutral-800/85 sm:px-5"
                    data-mobile-history-trigger
                    @click="openDrawer('history')">
                    <span class="flex min-w-0 items-center gap-3">
                        <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2.25M21 12a9 9 0 1 1-3.134-6.857" />
                            </svg>
                        </span>
                        <span>History</span>
                    </span>
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
                <a href="{{ route('news.index') }}"
                    class="ui-card-row-link">
                    <div class="ui-card-row justify-start gap-3 px-4 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 sm:px-5">
                        <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.5v9a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 16.5v-9m15 0A2.25 2.25 0 0 0 17.25 5.25H6.75A2.25 2.25 0 0 0 4.5 7.5m15 0v.75A2.25 2.25 0 0 1 17.25 10.5H6.75A2.25 2.25 0 0 1 4.5 8.25V7.5m4.5 6h6" />
                            </svg>
                        </span>
                        <span>News</span>
                    </div>
                </a>
                <a href="{{ route('page.show', 'handbook') }}"
                    class="ui-card-row-link">
                    <div class="ui-card-row justify-start gap-3 px-4 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 sm:px-5">
                        <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5A2.25 2.25 0 0 1 6 2.25h9.75A2.25 2.25 0 0 1 18 4.5v15.75a.75.75 0 0 1-1.28.53l-1.94-1.94a.75.75 0 0 0-1.06 0l-1.94 1.94a.75.75 0 0 1-1.06 0l-1.94-1.94a.75.75 0 0 0-1.06 0l-1.94 1.94A.75.75 0 0 1 3.75 20.25V4.5Z" />
                            </svg>
                        </span>
                        <span>Handbook</span>
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
                        <div class="ui-card-row justify-start gap-3 px-4 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 sm:px-5">
                            <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h12a2.25 2.25 0 0 0 2.25-2.25V3.75M3.75 3h16.5M9 20.25h6" />
                                </svg>
                            </span>
                            <span>Admin</span>
                        </div>
                    </a>
                @endif
                <button type="button"
                    class="ui-card-row w-full cursor-pointer justify-start gap-3 px-4 text-left text-base font-semibold leading-7 text-gray-900 transition hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-neutral-800/85 sm:px-5"
                    x-cloak
                    x-show="canInstallApp"
                    @click="installApp()"
                    data-mobile-install-app-trigger>
                    <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5v-9m0 9-3-3m3 3 3-3M3.75 17.25v.75A2.25 2.25 0 0 0 6 20.25h12A2.25 2.25 0 0 0 20.25 18v-.75" />
                        </svg>
                    </span>
                    <span>Install app</span>
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                        class="ui-card-row-link"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        <div class="ui-card-row justify-start gap-3 px-4 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 sm:px-5">
                            <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-7.5a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 6 21h7.5a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                </svg>
                            </span>
                            <span>Log out</span>
                        </div>
                    </a>
                </form>
            @else
                <button type="button"
                    class="ui-card-row w-full cursor-pointer justify-start gap-3 px-4 text-left text-base font-semibold leading-7 text-gray-900 transition hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-neutral-800/85 sm:px-5"
                    x-cloak
                    x-show="canInstallApp"
                    @click="installApp()"
                    data-mobile-install-app-trigger>
                    <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5v-9m0 9-3-3m3 3 3-3M3.75 17.25v.75A2.25 2.25 0 0 0 6 20.25h12A2.25 2.25 0 0 0 20.25 18v-.75" />
                        </svg>
                    </span>
                    <span>Install app</span>
                </button>
            @endif
            </div>
        </div>
    </div>
</div>
