<div class="hidden lg:flex lg:justify-end lg:gap-x-6">
    @if (@auth()->user())
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
                class="flex items-center gap-x-1 text-sm font-semibold leading-6 text-gray-900 dark:text-gray-100"
                aria-expanded="false" @click="toggle()" :aria-expanded="open">
                <img
                    src="{{ auth()->user()->avatar_url }}"
                    alt="{{ auth()->user()->name }} avatar"
                    class="h-8 w-8 rounded-full object-cover bg-gray-50">
                <span class="sr-only">Open user menu for {{ auth()->user()->name }}</span>
                <svg class="h-5 w-5 flex-none text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor"
                    aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            <div class="absolute right-0 top-full z-10 mt-3 w-56"
                x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1"
                @click="open = false">
                <div class="ui-card overflow-hidden dark:bg-zinc-800">
                    <div class="ui-card-rows">
                        <a href="{{ route('account.show') }}"
                            class="ui-card-row-link">
                            <div class="ui-card-row px-4 text-sm font-semibold text-gray-900 dark:text-gray-100 sm:px-5">
                                Account
                            </div>
                        </a>
                        <div x-cloak x-show="canInstallApp">
                            <button type="button"
                                class="ui-card-row w-full cursor-pointer px-4 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 sm:px-5"
                                @click="installApp()"
                                data-install-app-trigger>
                                Install app
                            </button>
                        </div>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('filament.admin.pages.dashboard') }}"
                                class="ui-card-row-link">
                                <div class="ui-card-row px-4 text-sm font-semibold text-gray-900 dark:text-gray-100 sm:px-5">
                                    Admin
                                </div>
                            </a>
                        @endif
                        @if ($is_impersonating ?? false)
                            <a class="ui-card-row-link"
                                href="{{ route('impersonation.leave') }}">
                                <div class="ui-card-row px-4 text-sm font-semibold text-gray-900 dark:text-gray-100 sm:px-5">
                                    Stop impersonating
                                </div>
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                                class="ui-card-row-link"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                <div class="ui-card-row px-4 text-sm font-semibold text-gray-900 dark:text-gray-100 sm:px-5">
                                    Log out
                                </div>
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <a href="{{ route('login') }}" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-100">
            Log in <span aria-hidden="true">&rarr;</span>
        </a>
    @endif
</div>
