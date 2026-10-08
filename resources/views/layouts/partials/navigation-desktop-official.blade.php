<div class="relative"
    x-data="{
        id: 'official',
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
        focusTrigger(position) {
            const navigationMenu = this.$el.closest('[data-navigation-menu]');
            const triggers = Array.from(navigationMenu.querySelectorAll('[data-navigation-menu-trigger]'));
            const currentTrigger = this.$el.querySelector('[data-navigation-menu-trigger]');
            const currentIndex = triggers.indexOf(currentTrigger);
            const nextIndex = position === 'first'
                ? 0
                : position === 'last'
                    ? triggers.length - 1
                    : (currentIndex + position + triggers.length) % triggers.length;

            triggers[nextIndex]?.focus();
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
    @keydown.escape.stop="close(); $el.querySelector('[data-navigation-menu-trigger]')?.focus()"
    @click.outside="close()"
    @nav-dropdown-open.window="if ($event.detail.id !== id) close()">
    <button type="button"
        class="group inline-flex h-8 w-max items-center justify-center gap-1.5 rounded-md bg-white px-2.5 py-2 text-sm leading-5 font-medium whitespace-nowrap text-gray-900 transition-[color,box-shadow] outline-none hover:bg-gray-100 hover:text-gray-900 focus:bg-gray-100 focus:text-gray-900 focus-visible:ring-2 focus-visible:ring-green-600/40 focus-visible:outline-1 data-[state=open]:bg-gray-100 data-[state=open]:text-gray-900 dark:bg-neutral-950 dark:text-gray-100 dark:hover:bg-neutral-800 dark:hover:text-gray-100 dark:focus:bg-neutral-800 dark:focus:text-gray-100 dark:data-[state=open]:bg-neutral-800 dark:data-[state=open]:text-gray-100 {{ $officialNavIsActive ? 'bg-gray-100 text-gray-900 dark:bg-neutral-800 dark:text-gray-100' : '' }}"
        @click="toggle()"
        @keydown.arrowdown.prevent="show(); $nextTick(() => $refs.menuContent.querySelector('a')?.focus())"
        @keydown.arrowright.prevent="focusTrigger(1)"
        @keydown.arrowleft.prevent="focusTrigger(-1)"
        @keydown.home.prevent="focusTrigger('first')"
        @keydown.end.prevent="focusTrigger('last')"
        :aria-expanded="open"
        :data-state="open ? 'open' : 'closed'"
        data-navigation-menu-trigger>
        Official
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="relative top-px ml-1 size-3 flex-none text-neutral-400 transition-transform duration-150 dark:text-neutral-500" :class="open ? 'rotate-180' : ''" aria-hidden="true">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M6 9l6 6l6 -6" />
        </svg>
    </button>

    <span x-show="open" x-cloak x-transition
        class="absolute left-1/2 top-full z-50 size-2 -translate-x-1/2 translate-y-1/2 rotate-45 bg-white ring-1 ring-gray-200 dark:bg-neutral-900 dark:ring-neutral-800"
        aria-hidden="true"></span>

    <div x-ref="menuContent"
        class="absolute left-0 top-full z-50 mt-1.5 w-72 origin-top-left overflow-hidden rounded-md border border-gray-200 bg-white p-2 pr-2.5 text-gray-900 shadow-lg shadow-black/5 dark:border-neutral-800 dark:bg-neutral-900 dark:text-gray-100 dark:shadow-black/20"
        x-show="open"
        x-cloak
        x-transition:enter="ui-motion-popover-in"
        x-transition:enter-start="translate-y-1 scale-95 opacity-0"
        x-transition:enter-end="translate-y-0 scale-100 opacity-100"
        x-transition:leave="ui-motion-popover-out"
        x-transition:leave-start="translate-y-0 scale-100 opacity-100"
        x-transition:leave-end="translate-y-1 scale-95 opacity-0"
        @mouseenter="cancelClose()"
        data-navigation-menu-content
        :data-state="open ? 'open' : 'closed'"
        data-official-nav>
        <div class="flex flex-col gap-1">
            <a href="{{ route('page.show', 'handbook') }}"
                class="block rounded-sm transition-colors hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                <div class="rounded-sm px-2 py-2 text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">
                    <span>Handbook</span>
                </div>
            </a>
            <a href="{{ route('downloads.index') }}"
                class="block rounded-sm transition-colors hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                <div class="rounded-sm px-2 py-2 text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">
                    <span>Downloads</span>
                </div>
            </a>
        </div>
    </div>
</div>
