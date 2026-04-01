<div class="relative z-99 duration-300 {{ $isOpen ? 'visible opacity-100' : 'invisible opacity-0' }}" role="dialog"
    aria-modal="true"
    x-data="{
        open: @entangle('isOpen').live,
        focusTimer: null,
        close() {
            if (this.focusTimer) {
                clearTimeout(this.focusTimer)
                this.focusTimer = null
            }

            this.open = false
            this.$wire.closeSearch()
        },
        focusInput() {
            if (this.focusTimer) {
                clearTimeout(this.focusTimer)
            }

            this.focusTimer = window.setTimeout(() => {
                this.$refs.searchInput?.focus({ preventScroll: true })
                this.focusTimer = null
            }, 75)
        },
    }"
    x-on:focus-first-search-result.stop="$el.querySelector('[data-search-result-link]')?.focus()"
    x-effect="if (open) { focusInput() }"
    x-on:keydown.escape.window="if (open) { close() }">

    <div class="fixed inset-0 bg-gray-500/25 transition-opacity dark:bg-black/70" x-show="open"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" aria-hidden="true"
        @click="close()"
    ></div>

    <div class="fixed inset-0 z-10 overflow-y-auto px-4 py-[12px] sm:p-6 md:p-20" x-show="open"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1">
        <div @click.outside="close()"
            class="mx-auto max-w-xl transform overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black/5 transition-all dark:bg-neutral-950 dark:ring-white/10"
            data-search-modal-shell>
            <div class="relative border-b border-gray-200 dark:border-neutral-800/80">
                <svg class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20"
                    fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                        clip-rule="evenodd" />
                </svg>
                <input type="search" id="searchInput" x-ref="searchInput" autocomplete="off"
                    wire:model.live.debounce.300ms="searchTerm"
                    x-on:keydown.down.prevent.stop="$dispatch('focus-first-search-result')"
                    class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 dark:text-gray-100 dark:placeholder:text-gray-500 sm:pr-24 sm:text-sm"
                    placeholder="Search players, teams, venues..." role="combobox"
                    aria-expanded="{{ ! empty($resultGroups) ? 'true' : 'false' }}" aria-controls="options">
                <div class="pointer-events-none absolute inset-y-0 right-4 hidden items-center sm:flex">
                    <span class="rounded-md border border-gray-200 bg-gray-50 px-2 py-1 text-[11px] font-semibold tracking-wide text-gray-400 dark:border-neutral-800 dark:bg-neutral-800 dark:text-gray-500">
                        Ctrl K
                    </span>
                </div>
            </div>

            @include('livewire.search-partials.loading-state')

            <div wire:loading.remove wire:target="searchTerm">
                @if ($searchTermLength < 3)
                    @include('livewire.search-partials.empty-prompt')
                @else
                    @if (! empty($resultGroups))
                        @include('livewire.search-partials.results')
                    @else
                        @include('livewire.search-partials.no-results')
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
