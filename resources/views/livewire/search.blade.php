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
        navigateToResult(event) {
            const href = event.currentTarget?.href

            if (!href || event.defaultPrevented) {
                return
            }

            if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return
            }

            event.preventDefault()
            window.location.assign(href)
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

    <div class="fixed inset-0 z-10 flex items-start justify-center overflow-y-auto p-2 sm:items-center" x-show="open"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1">
        <div @click.outside="close()"
            class="relative mx-auto w-full max-w-none transform overflow-hidden rounded-xl border border-gray-200/80 bg-white p-2 pb-11 text-gray-900 shadow-2xl shadow-black/10 ring-4 ring-gray-200/80 transition-all sm:max-w-lg dark:border-neutral-800 dark:bg-neutral-900 dark:text-gray-100 dark:ring-neutral-800"
            data-search-modal-shell>
            <div class="relative flex h-9 items-center rounded-md border border-gray-200 bg-gray-50/70 px-3 dark:border-neutral-800 dark:bg-neutral-800/50">
                <svg class="pointer-events-none mr-2 size-4 shrink-0 text-gray-400 dark:text-gray-500" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" />
                    <path d="m20 20-4-4" />
                </svg>
                <input type="text" id="searchInput" x-ref="searchInput" autocomplete="off"
                    wire:model.live.debounce.300ms="searchTerm"
                    x-on:keydown.down.prevent.stop="$dispatch('focus-first-search-result')"
                    class="h-9 min-w-0 flex-1 border-0 bg-transparent px-0 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-0 dark:text-gray-100 dark:placeholder:text-gray-500"
                    placeholder="Search players, teams, venues..." role="combobox"
                    aria-expanded="{{ ! empty($resultGroups) ? 'true' : 'false' }}" aria-controls="options">
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

            <div class="absolute inset-x-0 bottom-0 z-20 flex h-10 items-center gap-3 rounded-b-xl border-t border-gray-200 bg-gray-50 px-4 text-xs font-medium text-gray-500 dark:border-neutral-800 dark:bg-neutral-800 dark:text-neutral-400">
                <div class="flex items-center gap-1.5">
                    <kbd class="pointer-events-none flex h-5 items-center justify-center gap-1 rounded border border-gray-200 bg-white px-1 font-sans text-[0.7rem] font-medium text-gray-500 shadow-sm select-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">↑↓</kbd>
                    <span>Navigate</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <kbd class="pointer-events-none flex h-5 items-center justify-center rounded border border-gray-200 bg-white px-1 font-sans text-[0.7rem] font-medium text-gray-500 shadow-sm select-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">↵</kbd>
                    <span>Open</span>
                </div>
                <div class="ml-auto flex items-center gap-1.5">
                    <kbd class="pointer-events-none flex h-5 items-center justify-center rounded border border-gray-200 bg-white px-1 font-sans text-[0.7rem] font-medium text-gray-500 shadow-sm select-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">Esc</kbd>
                    <span>Close</span>
                </div>
            </div>
        </div>
    </div>
</div>
