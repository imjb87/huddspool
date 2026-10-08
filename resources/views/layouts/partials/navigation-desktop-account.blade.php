<div class="hidden items-center lg:flex lg:gap-1">
    @if (@auth()->user())
        <div class="relative"
            x-data="{
                id: 'account',
                open: false,
                closeTimer: null,
                prefersTap() {
                    return window.matchMedia('(hover: none), (pointer: coarse)').matches;
                },
                cancelClose() {
                    if (this.closeTimer) {
                        window.clearTimeout(this.closeTimer);
                        this.closeTimer = null;
                    }
                },
                scheduleClose() {
                    this.cancelClose();
                    this.closeTimer = window.setTimeout(() => {
                        this.open = false;
                        this.closeTimer = null;
                    }, 150);
                },
                show() {
                    this.cancelClose();
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
                        this.scheduleClose();
                    }
                },
                close() {
                    this.cancelClose();
                    this.open = false;
                },
                toggle() {
                    if (this.open) {
                        this.close();

                        return;
                    }

                    this.show();
                },
            }"
            @mouseenter="openOnHover()"
            @mouseleave="closeOnHover()"
            @focusin="show()"
            @focusout="if (! $el.contains($event.relatedTarget)) scheduleClose()"
            @keydown.escape.stop="close(); $el.querySelector('button')?.focus()"
            @click.outside="close()"
            @close.stop="close()"
            @nav-dropdown-open.window="if ($event.detail.id !== id) close()">
            <button type="button"
                class="group/button inline-flex h-[31px] shrink-0 items-center justify-center gap-1.5 rounded-[10px] border border-transparent bg-black px-2.5 text-[0.8rem] font-medium whitespace-nowrap text-white transition-[background-color,box-shadow,transform] duration-150 outline-none select-none hover:bg-black/80 focus-visible:ring-2 focus-visible:ring-black/50 dark:bg-gray-200 dark:text-gray-900 dark:hover:bg-gray-200/80 dark:focus-visible:ring-gray-200/50"
                aria-expanded="false"
                aria-label="Open user menu for {{ auth()->user()->name }}"
                @click="toggle()"
                :aria-expanded="open">
                <img src="{{ auth()->user()->avatar_url }}"
                    alt=""
                    class="size-5 rounded-full object-cover ring-1 ring-white/20 dark:ring-black/10">
                <span class="hidden max-w-32 truncate sm:inline">{{ auth()->user()->name }}</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3.5 opacity-80 transition-transform duration-150" :class="open ? 'rotate-180' : ''" aria-hidden="true">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M6 9l6 6l6 -6" />
                </svg>
                <span class="sr-only">Open user menu for {{ auth()->user()->name }}</span>
            </button>

            <span x-show="open" x-cloak x-transition
                class="absolute left-1/2 top-full z-50 size-2 -translate-x-1/2 translate-y-1/2 rotate-45 bg-white ring-1 ring-gray-200 dark:bg-neutral-900 dark:ring-neutral-800"
                aria-hidden="true"></span>

            <div x-ref="menuContent"
                class="absolute right-0 top-full z-50 mt-1.5 w-72 origin-top-right overflow-hidden rounded-md border border-gray-200 bg-white p-2 pr-2.5 text-gray-900 shadow-lg shadow-black/5 dark:border-neutral-800 dark:bg-neutral-900 dark:text-gray-100 dark:shadow-black/20"
                x-show="open"
                x-cloak
                x-transition:enter="ui-motion-popover-in"
                x-transition:enter-start="translate-y-1 scale-95 opacity-0"
                x-transition:enter-end="translate-y-0 scale-100 opacity-100"
                x-transition:leave="ui-motion-popover-out"
                x-transition:leave-start="translate-y-0 scale-100 opacity-100"
                x-transition:leave-end="translate-y-1 scale-95 opacity-0"
                @mouseenter="cancelClose()">
                <div class="flex flex-col gap-1">
                    <a href="{{ route('account.show') }}"
                        class="block rounded-sm transition-colors hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                        <div class="rounded-sm px-2 py-2 text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">
                            <span>Your account</span>
                        </div>
                    </a>
                    <button type="button"
                        class="block w-full rounded-sm px-2 py-2 text-left text-sm leading-5 font-medium text-gray-900 transition-colors hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:text-gray-100 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                        x-cloak x-show="canInstallApp"
                        @click="installApp()"
                        data-install-app-trigger>
                        <span>Install app</span>
                    </button>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('filament.admin.pages.dashboard') }}"
                            class="block rounded-sm transition-colors hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                            <div class="rounded-sm px-2 py-2 text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">
                                <span>Admin</span>
                            </div>
                        </a>
                    @endif
                    @if ($is_impersonating ?? false)
                        <a href="{{ route('impersonation.leave') }}"
                            class="block rounded-sm transition-colors hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                            <div class="rounded-sm px-2 py-2 text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">
                                <span>Stop impersonating</span>
                            </div>
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}"
                            class="block rounded-sm transition-colors hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            <div class="rounded-sm px-2 py-2 text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">
                                <span>Log out</span>
                            </div>
                        </a>
                    </form>
                </div>
            </div>
        </div>
    @else
        <a href="{{ route('login') }}"
            class="group/button inline-flex h-[31px] shrink-0 items-center justify-center gap-1.5 rounded-[10px] border border-transparent bg-black px-2.5 text-[0.8rem] font-medium whitespace-nowrap text-white transition-[background-color,box-shadow,transform] duration-150 outline-none select-none hover:bg-black/80 focus-visible:ring-2 focus-visible:ring-black/50 dark:bg-gray-200 dark:text-gray-900 dark:hover:bg-gray-200/80 dark:focus-visible:ring-gray-200/50"
            aria-label="Log in"
            data-header-login-link>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M9 8v-2a2 2 0 0 1 2 -2h7a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2" />
                <path d="M3 12h13l-3 -3" />
                <path d="M13 15l3 -3" />
            </svg>
            <span>Log in</span>
        </a>
    @endif
</div>
