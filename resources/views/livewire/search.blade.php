@php
    $normalizedSearchTerm = is_string($searchTerm) ? trim($searchTerm) : '';
    $searchTermLength = strlen($normalizedSearchTerm);
    $resultGroups = $this->resultGroups;
@endphp

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
            class="mx-auto max-w-xl transform overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black/5 transition-all dark:bg-zinc-900 dark:ring-white/10"
            data-search-modal-shell>
            <div class="relative border-b border-gray-200 dark:border-zinc-800/80">
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
                    <span class="rounded-md border border-gray-200 bg-gray-50 px-2 py-1 text-[11px] font-semibold tracking-wide text-gray-400 dark:border-zinc-700 dark:bg-zinc-700 dark:text-gray-500">
                        Ctrl K
                    </span>
                </div>
            </div>

            <div class="w-full px-4 py-3 sm:px-4" wire:loading wire:target="searchTerm" data-search-loading-state>
                <div class="w-full space-y-4" data-search-loading-skeleton>
                    @foreach (range(1, 2) as $groupIndex)
                        <div class="w-full border-t border-gray-200 first:border-t-0 dark:border-zinc-800/80">
                            <div class="px-1 pb-2 pt-3">
                                <div class="h-3 w-20 rounded-full bg-gray-200 dark:bg-zinc-800"></div>
                            </div>
                            <div class="w-full space-y-1">
                                @foreach (range(1, 3) as $rowIndex)
                                    <div class="flex w-full items-center gap-4 rounded-xl border border-gray-200 px-4 py-3 dark:border-zinc-800/80"
                                        wire:key="search-loading-group-{{ $groupIndex }}-row-{{ $rowIndex }}">
                                        <div class="min-w-0 flex flex-1 items-start gap-3">
                                            <div class="h-9 w-9 shrink-0 rounded-full bg-gray-200 dark:bg-zinc-800"></div>
                                            <div class="min-w-0 flex-1 space-y-2">
                                                <div class="h-3.5 w-32 rounded-full bg-gray-200 dark:bg-zinc-800"></div>
                                                <div class="h-3 w-24 rounded-full bg-gray-100 dark:bg-zinc-800/70"></div>
                                            </div>
                                        </div>
                                        <div class="ml-auto h-6 w-14 shrink-0 rounded-full bg-gray-100 dark:bg-zinc-800/70"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div wire:loading.remove wire:target="searchTerm">
                @if ($searchTermLength < 3)
                    <div class="px-6 py-14 text-center text-sm sm:px-14" data-search-empty-prompt>
                        <svg class="mx-auto h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.115 5.19l.319 1.913A6 6 0 008.11 10.36L9.75 12l-.387.775c-.217.433-.132.956.21 1.298l1.348 1.348c.21.21.329.497.329.795v1.089c0 .426.24.815.622 1.006l.153.076c.433.217.956.132 1.298-.21l.723-.723a8.7 8.7 0 002.288-4.042 1.087 1.087 0 00-.358-1.099l-1.33-1.108c-.251-.21-.582-.299-.905-.245l-1.17.195a1.125 1.125 0 01-.98-.314l-.295-.295a1.125 1.125 0 010-1.591l.13-.132a1.125 1.125 0 011.3-.21l.603.302a.809.809 0 001.086-1.086L14.25 7.5l1.256-.837a4.5 4.5 0 001.528-1.732l.146-.292M6.115 5.19A9 9 0 1017.18 4.64M6.115 5.19A8.965 8.965 0 0112 3c1.929 0 3.716.607 5.18 1.64" />
                        </svg>
                        <p class="mt-4 font-semibold text-gray-900 dark:text-gray-100">Search for players, teams and venues</p>
                        <p class="mt-2 text-gray-500 dark:text-gray-400">Quickly find what you’re looking for by running a global search.</p>
                    </div>
                @else
                    @if (!empty($resultGroups))
                        <ul class="max-h-[28rem] overflow-y-auto px-3 py-3 sm:px-4" id="options"
                            x-on:keydown.up.prevent="$focus.wrap().previous()"
                            x-on:keydown.down.prevent="$focus.wrap().next()"
                            role="listbox"
                            data-search-results-shell>
                            @foreach ($resultGroups as $name => $group)
                                @if ($group['results']->isNotEmpty())
                                    <li class="border-t border-gray-200 first:border-t-0 dark:border-zinc-800/80" wire:key="search-group-{{ $name }}">
                                        <div class="px-1 pb-2 pt-3">
                                            <h2 class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">
                                                {{ $group['heading'] }}</h2>
                                        </div>
                                        <div class="space-y-1 text-sm text-gray-800 dark:text-gray-200" data-search-result-group>
                                            @foreach ($group['results'] as $item)
                                                @if (is_object($item))
                                                    <a class="flex items-start justify-between gap-4 rounded-xl border border-transparent px-4 py-3 transition duration-200 hover:border-gray-200 hover:bg-gray-50 focus:border-gray-200 focus:bg-gray-50 focus:outline-none dark:hover:border-zinc-800 dark:hover:bg-zinc-800/80 dark:focus:border-zinc-800 dark:focus:bg-zinc-800/80"
                                                        href="{{ route($group['route'] . '.show', $item->id) }}"
                                                        data-search-result-link
                                                        wire:key="search-result-{{ $name }}-{{ $item->id }}"
                                                        x-on:click="close()">
                                                        <div class="min-w-0 flex flex-1 items-start gap-3">
                                                            @if ($name === 'players')
                                                                <img src="{{ $item->avatar_url }}"
                                                                    alt="{{ $item->name }}"
                                                                    class="mt-0.5 h-9 w-9 shrink-0 rounded-full object-cover ring-1 ring-gray-200 dark:ring-zinc-700"
                                                                    data-search-player-avatar>
                                                            @endif
                                                            <div class="min-w-0">
                                                                <p class="truncate font-semibold text-gray-900 dark:text-gray-100">{{ $item->name }}</p>
                                                            @if ($name === 'players')
                                                                <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">
                                                                    {{ $item->team?->name ?? 'No team assigned' }}
                                                                </p>
                                                            @elseif ($name === 'teams')
                                                                <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">
                                                                    {{ $item->openSection()?->name ?? 'Open section unavailable' }}
                                                                </p>
                                                            @elseif ($name === 'venues')
                                                                <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">
                                                                    {{ $item->address }}
                                                                </p>
                                                            @endif
                                                            </div>
                                                        </div>
                                                        <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-semibold tracking-wide text-gray-500 dark:bg-zinc-800 dark:text-gray-400">
                                                            {{ $group['badge'] }}
                                                        </span>
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <div class="px-6 py-14 text-center text-sm sm:px-14" wire:loading.remove wire:target="searchTerm" data-search-no-results>
                            <svg class="mx-auto h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
                            </svg>
                            <p class="mt-4 font-semibold text-gray-900 dark:text-gray-100">No results found</p>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">We couldn’t find anything with that term. Please try again.</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
