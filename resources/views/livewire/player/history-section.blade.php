<div>
    @if ($this->allHistory->isNotEmpty())
        <section class="ui-section" data-player-history-section>
            <div class="ui-shell-grid">
                <div class="ui-section-intro gap-2">
                    <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-history size-5 text-neutral-700 dark:text-neutral-200">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 8l0 4l2 2" />
                            <path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" />
                        </svg>
                    </span>
                    <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                        <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">History</h2>
                        <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                            Season-by-season playing history with archived team and section details.
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="ui-card">
                        <div class="ui-averages-item-group"
                            wire:loading.remove
                            wire:target="previousPage, nextPage"
                            data-player-history-list>
                            <div class="ui-average-item ui-average-item-header" data-slot="item" data-variant="muted" data-size="default">
                                <div class="ui-average-item-content" data-slot="item-content">
                                    <div class="flex min-w-0 items-center gap-2 sm:flex-1 sm:gap-3"></div>

                                    <div class="ui-average-item-stats" data-slot="item-actions">
                                        <div class="ui-card-column-header w-12 sm:w-16">Played</div>
                                        <div class="ui-card-column-header w-12 sm:w-16">Won</div>
                                        <div class="ui-card-column-header hidden w-12 sm:block sm:w-16">Lost</div>
                                    </div>
                                </div>
                            </div>

                            @foreach ($this->historyRows as $entry)
                                @if ($entry['history_link'] ?? null)
                                    <a href="{{ $entry['history_link'] }}"
                                        class="ui-average-item focus-visible:ring-2 focus-visible:ring-gray-900/20 dark:focus-visible:ring-neutral-100/20"
                                        wire:key="player-history-{{ $entry['season_id'] }}-{{ $entry['section_id'] }}-{{ $loop->index }}"
                                        data-slot="item"
                                        data-variant="muted"
                                        data-size="default">
                                @else
                                    <div class="ui-average-item"
                                        wire:key="player-history-{{ $entry['season_id'] }}-{{ $entry['section_id'] }}-{{ $loop->index }}"
                                        data-slot="item"
                                        data-variant="muted"
                                        data-size="default">
                                @endif
                                        <div class="ui-average-item-content" data-slot="item-content">
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate whitespace-nowrap text-sm font-semibold text-neutral-950 dark:text-neutral-50">{{ $entry['season_name'] }}</p>
                                                <p class="mt-1 truncate whitespace-nowrap text-xs text-neutral-500 dark:text-neutral-400">{{ $entry['team_name'] ?? 'Team TBC' }}</p>
                                                <p class="mt-1 truncate whitespace-nowrap text-xs text-neutral-500 dark:text-neutral-400">{{ $entry['section_name'] ?? 'Section TBC' }}</p>
                                            </div>

                                            <div class="ui-average-item-stats" data-slot="item-actions">
                                                <div class="w-12 sm:w-16">
                                                    <p class="text-sm font-semibold text-neutral-950 dark:text-neutral-50">{{ $entry['played'] }}</p>
                                                    <span class="invisible inline-flex items-center justify-center rounded-md px-1.5 py-0.5 text-[10px] font-semibold">0%</span>
                                                </div>
                                                <div class="w-12 sm:w-16">
                                                    <p class="text-sm font-semibold text-green-700 dark:text-green-400">{{ $entry['wins'] }}</p>
                                                    <span class="inline-flex items-center justify-center rounded-md bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-950/50 dark:text-green-300">{{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($entry['win_percentage']) }}%</span>
                                                </div>
                                                <div class="hidden w-12 sm:block sm:w-16">
                                                    <p class="text-sm font-semibold text-red-700 dark:text-red-400">{{ $entry['losses'] }}</p>
                                                    <span class="inline-flex items-center justify-center rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-950/50 dark:text-red-300">{{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($entry['loss_percentage']) }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                @if ($entry['history_link'] ?? null)
                                    </a>
                                @else
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="animate-pulse" wire:loading.block wire:target="previousPage, nextPage" data-player-history-loading>
                            <div class="ui-averages-item-group">
                                @foreach (range(1, 5) as $row)
                                    <div class="ui-average-item">
                                        <div class="ui-average-item-content">
                                            <div class="min-w-0 flex-1">
                                                <div class="h-4 w-32 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                                <div class="mt-2 h-3 w-20 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                                <div class="mt-2 h-3 w-16 rounded-full bg-gray-100 dark:bg-neutral-900/70"></div>
                                            </div>

                                            <div class="ui-average-item-stats">
                                                @foreach (range(1, 3) as $column)
                                                    <div class="{{ $column === 3 ? 'hidden sm:block ' : '' }}w-12 sm:w-16">
                                                        <div class="flex flex-col items-center gap-1">
                                                            <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-10"></div>
                                                            <div class="h-5 w-12 rounded-md {{ $column === 1 ? 'opacity-0' : 'bg-gray-200 dark:bg-neutral-800' }}"></div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if ($this->lastPage() > 1)
                            <div class="border-t border-border px-5 py-4" data-player-history-controls>
                                <nav class="ui-pagination" aria-label="Player history pagination">
                                    <button wire:click="previousPage"
                                        wire:loading.attr="disabled"
                                        class="ui-pagination-link rounded-full"
                                        aria-label="Previous page"
                                        type="button"
                                        @disabled($page === 1)>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left size-4" aria-hidden="true">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M15 6l-6 6l6 6" />
                                        </svg>
                                        <span class="hidden sm:inline">Previous</span>
                                    </button>

                                    <span class="ui-pagination-current" aria-live="polite">Page {{ $page }}</span>

                                    <button wire:click="nextPage"
                                        wire:loading.attr="disabled"
                                        class="ui-pagination-link rounded-full"
                                        aria-label="Next page"
                                        type="button"
                                        @disabled(! $this->hasNextPage())>
                                        <span class="hidden sm:inline">Next</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right size-4" aria-hidden="true">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M9 6l6 6l-6 6" />
                                        </svg>
                                    </button>
                                </nav>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>
