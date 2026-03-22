<div class="hidden lg:flex lg:flex-1 lg:justify-end lg:gap-x-6">
    <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-300"
        @click="toggleTheme()" aria-label="Toggle dark mode" data-theme-toggle>
        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" x-cloak x-show="theme !== 'dark'">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
        </svg>
        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" x-cloak x-show="theme === 'dark'">
            <path fill-rule="evenodd" d="M10 2.75a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0V3.5a.75.75 0 01.75-.75zM10 14a4 4 0 100-8 4 4 0 000 8zm0 3.25a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0V18a.75.75 0 01.75-.75zM4.166 4.166a.75.75 0 011.06 0l1.06 1.061a.75.75 0 11-1.06 1.06L4.166 5.23a.75.75 0 010-1.06zm9.548 9.548a.75.75 0 011.06 0l1.06 1.06a.75.75 0 01-1.06 1.061l-1.06-1.06a.75.75 0 010-1.061zM2.75 10a.75.75 0 01.75-.75H5a.75.75 0 010 1.5H3.5A.75.75 0 012.75 10zm12.25 0a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5H15.75A.75.75 0 0115 10zM5.227 13.713a.75.75 0 011.06 1.061l-1.06 1.06a.75.75 0 11-1.061-1.06l1.061-1.06zm10.607-9.547a.75.75 0 010 1.06l-1.06 1.061a.75.75 0 11-1.061-1.06l1.06-1.061a.75.75 0 011.061 0z" clip-rule="evenodd" />
        </svg>
    </button>
    <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-300"
        data-site-search-trigger aria-label="Open search">
        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd"
                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                clip-rule="evenodd" />
        </svg>
    </button>
    @if (@auth()->user())
        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @click.away="open = false" @close.stop="open = false">
            <button type="button"
                class="flex items-center gap-x-1 text-sm font-semibold leading-6 text-gray-900 dark:text-gray-100"
                aria-expanded="false" @click="open = !open" :aria-expanded="open">
                <img
                    src="{{ auth()->user()->avatar_url }}"
                    alt="{{ auth()->user()->name }} avatar"
                    class="h-8 w-8 rounded-full object-cover"
                >
                <svg class="h-5 w-5 flex-none text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor"
                    aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            <div class="absolute right-0 top-full z-10 mt-3 w-56 rounded-xl bg-white p-2 shadow-lg ring-1 ring-gray-900/5 dark:bg-zinc-900 dark:ring-white/10"
                x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1"
                @click="open = false">
                <div class="py-1">
                    <a href="{{ route('account.show') }}"
                        class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800">
                        Account
                    </a>
                </div>
                <div class="py-1" x-cloak x-show="canInstallApp">
                    <button type="button"
                        class="block w-full rounded-md py-2 pl-3 pr-4 text-left text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800"
                        @click="installApp()"
                        data-install-app-trigger>
                        Install app
                    </button>
                </div>
                @if (auth()->user()->isAdmin())
                    <div class="py-1">
                        <a href="{{ route('filament.admin.pages.dashboard') }}"
                            class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800">
                            Admin
                        </a>
                    </div>
                @endif
                @if ($is_impersonating ?? false)
                    <div class="py-1">
                        <a class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800"
                            href="{{ route('impersonation.leave') }}">
                            Stop impersonating
                        </a>
                    </div>
                @endif
                <div class="py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}"
                            class="block rounded-md py-2 pl-3 pr-4 text-sm font-semibold leading-5 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            Log out
                        </a>
                    </form>
                </div>
            </div>
        </div>
    @else
        <a href="{{ route('login') }}" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-100">
            Log in <span aria-hidden="true">&rarr;</span>
        </a>
    @endif
</div>
