<div>
    @if ($this->allHistory->isNotEmpty())
        <section class="ui-section" data-player-history-section>
            <div class="ui-shell-grid">
                <div class="ui-section-intro">
                    <div class="ui-section-intro-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2.25m6-2.25a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <div class="ui-section-intro-copy">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">History</h3>
                        <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                            Season-by-season playing history with archived team and section details.
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="ui-card">
                        <div class="ui-card-column-headings px-4 sm:px-5">
                            <div class="flex min-w-0 items-center gap-3 sm:gap-4"></div>

                            <div class="ml-auto flex shrink-0 items-start gap-2 text-center sm:gap-5">
                                <div class="w-12 sm:w-16">
                                    <p class="ui-card-column-header">Played</p>
                                </div>
                                <div class="w-12 sm:w-16">
                                    <p class="ui-card-column-header">Won</p>
                                </div>
                                <div class="w-12 sm:w-16">
                                    <p class="ui-card-column-header">Lost</p>
                                </div>
                            </div>
                        </div>

                        <div class="ui-card-rows"
                            wire:loading.remove
                            wire:target="previousPage, nextPage">
                            @foreach ($this->historyRows as $entry)
                                <div wire:key="player-history-{{ $entry['season_id'] }}-{{ $entry['section_id'] }}-{{ $loop->index }}">
                                    @if ($entry['history_link'] ?? null)
                                        <a href="{{ $entry['history_link'] }}" class="ui-card-row-link">
                                    @endif
                                    <div class="ui-card-row items-center px-4 sm:px-5">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $entry['season_name'] }}</p>
                                            <p class="mt-1 truncate text-sm text-gray-700 dark:text-gray-300">{{ $entry['team_name'] ?? 'Team TBC' }}</p>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $entry['section_name'] ?? 'Section TBC' }}</p>
                                        </div>

                                        <div class="ml-auto flex shrink-0 items-start gap-2 self-center text-center sm:gap-5">
                                            <div class="w-12 sm:w-16">
                                                <p class="text-sm font-semibold leading-5 text-gray-900 dark:text-gray-100">{{ $entry['played'] }}</p>
                                                <span class="mt-1 inline-flex h-[18px] items-center text-[10px] font-semibold text-transparent">0%</span>
                                            </div>
                                            <div class="w-12 sm:w-16">
                                                <p class="text-sm font-semibold leading-5 text-green-700 dark:text-green-400">{{ $entry['wins'] }}</p>
                                                <span class="mt-1 inline-flex items-center rounded-md bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-500/15 dark:text-green-300">{{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($entry['win_percentage']) }}%</span>
                                            </div>
                                            <div class="w-12 sm:w-16">
                                                <p class="text-sm font-semibold leading-5 text-red-700 dark:text-red-400">{{ $entry['losses'] }}</p>
                                                <span class="mt-1 inline-flex items-center rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-500/15 dark:text-red-300">{{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($entry['loss_percentage']) }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($entry['history_link'] ?? null)
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="animate-pulse"
                            wire:loading.block
                            wire:target="previousPage, nextPage"
                            data-player-history-loading>
                            <div class="ui-card-rows">
                                @foreach (range(1, 5) as $row)
                                    <div class="ui-card-row items-center px-4 sm:px-5">
                                        <div class="min-w-0 flex-1">
                                            <div class="h-4 w-32 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                            <div class="mt-2 h-3 w-20 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                            <div class="mt-2 h-3 w-16 rounded-full bg-gray-100 dark:bg-neutral-900/70"></div>
                                        </div>

                                        <div class="ml-auto flex shrink-0 items-center gap-2 sm:gap-5">
                                            @foreach (range(1, 3) as $column)
                                                <div class="w-12 sm:w-16">
                                                    <div class="flex flex-col items-center gap-1">
                                                        <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-10"></div>
                                                        <div class="h-5 w-12 rounded-md {{ $column === 1 ? 'opacity-0' : 'bg-gray-200 dark:bg-neutral-800' }}"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if ($this->lastPage() > 1)
                        <div class="pt-5 pb-4 lg:pt-5 lg:pb-6" data-player-history-controls>
                            <div class="flex items-center justify-between gap-4">
                                <button wire:click="previousPage"
                                    wire:loading.attr="disabled"
                                    class="ui-button-primary min-w-24 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0"
                                    aria-label="Previous"
                                    @disabled($page === 1)>
                                    Previous
                                </button>

                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    Page {{ $page }}
                                </span>

                                <button wire:click="nextPage"
                                    wire:loading.attr="disabled"
                                    class="ui-button-primary min-w-24 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0"
                                    aria-label="Next"
                                    @disabled(! $this->hasNextPage())>
                                    Next
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif
</div>
