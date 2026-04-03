<div class="hidden lg:flex lg:justify-end lg:gap-x-4">
    @if (@auth()->user())
        @include('components.account.⚡notifications-dropdown')

        <div class="relative"
            x-data="{
                id: 'account',
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
            @click.away="open = false"
            @close.stop="open = false"
            @nav-dropdown-open.window="if ($event.detail.id !== id) open = false">
            <button type="button"
                class="flex items-center text-sm font-semibold leading-6 text-neutral-900 dark:text-neutral-100"
                aria-expanded="false"
                @click="toggle()"
                :aria-expanded="open">
                <span class="relative">
                    <img
                        src="{{ auth()->user()->avatar_url }}"
                        alt="{{ auth()->user()->name }} avatar"
                        class="h-8 w-8 rounded-full object-cover bg-neutral-50">
                </span>
                <span class="sr-only">Open user menu for {{ auth()->user()->name }}</span>
            </button>

            <div class="absolute right-0 top-full z-10 mt-3 w-72"
                x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1">
                <div class="ui-card overflow-hidden">
                    <div class="ui-card-rows">
                        <div class="ui-card-row px-4 sm:px-5">
                            @include('layouts.partials.theme-switcher', ['fullWidth' => true])
                        </div>
                        <a href="{{ route('account.show') }}"
                            class="ui-card-row-link">
                            <div class="ui-card-row justify-start gap-3 px-4 sm:px-5">
                                <img
                                    src="{{ auth()->user()->avatar_url }}"
                                    alt="{{ auth()->user()->name }} avatar"
                                    class="size-8 rounded-full object-cover">
                                <span class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ auth()->user()->name }}</span>
                            </div>
                        </a>
                        <a href="{{ route('page.show', 'handbook') }}"
                            class="ui-card-row-link">
                            <div class="ui-card-row justify-start gap-3 px-4 text-sm font-semibold text-neutral-900 dark:text-neutral-100 sm:px-5">
                                <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5A2.25 2.25 0 0 1 6 2.25h9.75A2.25 2.25 0 0 1 18 4.5v15.75a.75.75 0 0 1-1.28.53l-1.94-1.94a.75.75 0 0 0-1.06 0l-1.94 1.94a.75.75 0 0 1-1.06 0l-1.94-1.94a.75.75 0 0 0-1.06 0l-1.94 1.94A.75.75 0 0 1 3.75 20.25V4.5Z" />
                                    </svg>
                                </span>
                                <span>Handbook</span>
                            </div>
                        </a>
                        <button type="button"
                            class="ui-card-row w-full cursor-pointer justify-start gap-3 px-4 text-left text-sm font-semibold text-neutral-900 transition hover:bg-neutral-100 dark:text-neutral-100 dark:hover:bg-neutral-800 sm:px-5"
                            x-cloak x-show="canInstallApp"
                            @click="installApp()"
                            data-install-app-trigger>
                            <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5v-9m0 9-3-3m3 3 3-3M3.75 17.25v.75A2.25 2.25 0 0 0 6 20.25h12A2.25 2.25 0 0 0 20.25 18v-.75" />
                                </svg>
                            </span>
                            <span>Install app</span>
                        </button>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('filament.admin.pages.dashboard') }}"
                                class="ui-card-row-link">
                                <div class="ui-card-row justify-start gap-3 px-4 text-sm font-semibold text-neutral-900 dark:text-neutral-100 sm:px-5">
                                    <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h12a2.25 2.25 0 0 0 2.25-2.25V3.75M3.75 3h16.5M9 20.25h6" />
                                        </svg>
                                    </span>
                                    <span>Admin</span>
                                </div>
                            </a>
                        @endif
                        @if ($is_impersonating ?? false)
                            <a class="ui-card-row-link"
                                href="{{ route('impersonation.leave') }}">
                                <div class="ui-card-row px-4 text-sm font-semibold text-neutral-900 dark:text-neutral-100 sm:px-5">
                                    Stop impersonating
                                </div>
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                                class="ui-card-row-link"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                <div class="ui-card-row justify-start gap-3 px-4 text-sm font-semibold text-neutral-900 dark:text-neutral-100 sm:px-5">
                                    <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-7.5a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 6 21h7.5a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                        </svg>
                                    </span>
                                    <span>Log out</span>
                                </div>
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="relative flex items-center"
            x-data="{
                id: 'guest-settings',
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
            @click.away="open = false"
            @close.stop="open = false"
            @nav-dropdown-open.window="if ($event.detail.id !== id) open = false">
            <button type="button"
                class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-neutral-700 dark:text-neutral-300"
                aria-expanded="false" @click="toggle()" :aria-expanded="open">
                <span class="sr-only">Open settings menu</span>
                <span class="block w-5 flex items-center justify-center" aria-hidden="true">
                    <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                    </svg>
                </span>
            </button>
            <div class="absolute right-0 top-full z-10 mt-3 w-56"
                x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1">
                <div class="ui-card overflow-hidden dark:bg-neutral-900">
                    <div class="ui-card-rows">
                        <div class="ui-card-row px-4 sm:px-5">
                            @include('layouts.partials.theme-switcher', ['fullWidth' => true])
                        </div>
                        <a href="{{ route('login') }}" class="ui-card-row-link">
                            <div class="ui-card-row justify-start gap-3 px-4 text-sm font-semibold text-neutral-900 dark:text-neutral-100 sm:px-5">
                                <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-7.5a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 6 21h7.5a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                    </svg>
                                </span>
                                <span>Log in</span>
                            </div>
                        </a>
                        <a href="{{ route('page.show', 'handbook') }}" class="ui-card-row-link">
                            <div class="ui-card-row justify-start gap-3 px-4 text-sm font-semibold text-neutral-900 dark:text-neutral-100 sm:px-5">
                                <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5A2.25 2.25 0 0 1 6 2.25h9.75A2.25 2.25 0 0 1 18 4.5v15.75a.75.75 0 0 1-1.28.53l-1.94-1.94a.75.75 0 0 0-1.06 0l-1.94 1.94a.75.75 0 0 1-1.06 0l-1.94-1.94a.75.75 0 0 0-1.06 0l-1.94 1.94A.75.75 0 0 1 3.75 20.25V4.5Z" />
                                    </svg>
                                </span>
                                <span>Handbook</span>
                            </div>
                        </a>
                        <button type="button"
                            class="ui-card-row w-full cursor-pointer justify-start gap-3 px-4 text-left text-sm font-semibold text-neutral-900 transition hover:bg-neutral-100 dark:text-neutral-100 dark:hover:bg-neutral-800/85 sm:px-5"
                            x-cloak
                            x-show="canInstallApp"
                            @click="installApp()"
                            data-install-app-trigger>
                            <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5v-9m0 9-3-3m3 3 3-3M3.75 17.25v.75A2.25 2.25 0 0 0 6 20.25h12A2.25 2.25 0 0 0 20.25 18v-.75" />
                                </svg>
                            </span>
                            <span>Install app</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
