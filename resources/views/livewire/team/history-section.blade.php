<div>
    @if ($this->allHistory->isNotEmpty())
        <section class="ui-section" data-team-history-section>
            <div class="ui-shell-grid">
                <div class="ui-section-intro gap-2">
                    <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-history size-5 text-neutral-700 dark:text-neutral-200">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 8l0 4l2 2" />
                            <path d="M3.05 11a9 9 0 1 0 .5 -4m-.5 -4v4h4" />
                        </svg>
                    </span>
                    <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                        <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">History</h2>
                        <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                            Season-by-season record for this team across previous campaigns.
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="ui-card ui-standings-card">
                        <div class="ui-standings-item-group" wire:loading.remove wire:target="previousPage, nextPage" data-team-history-list>
                            <div class="ui-standings-item ui-standings-item-header" data-slot="item" data-variant="muted" data-size="default">
                                <div class="ui-standings-item-content" data-slot="item-content">
                                    <div class="flex min-w-0 items-center gap-2 sm:flex-1 sm:gap-3"></div>

                                    <div class="ml-auto grid shrink-0 grid-cols-6 gap-1.5 text-center sm:gap-2" data-slot="item-actions">
                                        <div class="ui-card-column-header w-8 sm:w-9">Pos</div>
                                        <div class="ui-card-column-header w-8 sm:w-9">Pl</div>
                                        <div class="ui-card-column-header w-8 sm:w-9">W</div>
                                        <div class="ui-card-column-header w-8 sm:w-9">D</div>
                                        <div class="ui-card-column-header w-8 sm:w-9">L</div>
                                        <div class="ui-card-column-header w-8 sm:w-9">Pts</div>
                                    </div>
                                </div>
                            </div>

                            @foreach ($this->historyRows as $historyRow)
                                @if ($historyRow->history_link)
                                    <a href="{{ $historyRow->history_link }}"
                                        class="ui-standings-item"
                                        wire:key="team-history-{{ $historyRow->season_id }}-{{ $historyRow->ruleset_id }}"
                                        data-slot="item"
                                        data-variant="muted"
                                        data-size="default">
                                @else
                                    <div class="ui-standings-item"
                                        wire:key="team-history-{{ $historyRow->season_id }}-{{ $historyRow->ruleset_id }}"
                                        data-slot="item"
                                        data-variant="muted"
                                        data-size="default">
                                @endif
                                        <div class="ui-standings-item-content" data-slot="item-content">
                                            <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">
                                                <div class="min-w-0">
                                                    <p class="truncate whitespace-nowrap text-sm font-semibold text-neutral-950 dark:text-neutral-50">{{ $historyRow->season_name }}</p>
                                                    <p class="mt-1 truncate whitespace-nowrap text-xs text-neutral-500 dark:text-neutral-400">{{ $historyRow->section_name }}</p>
                                                </div>
                                            </div>

                                            <div class="ml-auto grid shrink-0 grid-cols-6 gap-1.5 text-center sm:gap-2" data-slot="item-actions">
                                                <div class="w-8 sm:w-9"><p class="text-sm font-semibold text-neutral-950 dark:text-neutral-50">{{ $historyRow->position_label }}</p></div>
                                                <div class="w-8 sm:w-9"><p class="text-sm font-semibold text-neutral-950 dark:text-neutral-50">{{ $historyRow->played }}</p></div>
                                                <div class="w-8 sm:w-9"><p class="text-sm font-semibold text-green-700 dark:text-green-400">{{ $historyRow->wins }}</p></div>
                                                <div class="w-8 sm:w-9"><p class="text-sm font-semibold text-neutral-950 dark:text-neutral-50">{{ $historyRow->draws }}</p></div>
                                                <div class="w-8 sm:w-9"><p class="text-sm font-semibold text-red-700 dark:text-red-400">{{ $historyRow->losses }}</p></div>
                                                <div class="w-8 sm:w-9"><p class="text-sm font-semibold text-neutral-950 dark:text-neutral-50">{{ $historyRow->points }}</p></div>
                                            </div>
                                        </div>
                                @if ($historyRow->history_link)
                                    </a>
                                @else
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="animate-pulse" wire:loading.block wire:target="previousPage, nextPage" data-team-history-loading>
                            <div class="ui-standings-item-group">
                                @foreach (range(1, 5) as $row)
                                    <div class="ui-standings-item">
                                        <div class="ui-standings-item-content">
                                            <div class="min-w-0 flex-1">
                                                <div class="h-4 w-32 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                                <div class="mt-2 h-3 w-20 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                            </div>

                                            <div class="ml-auto grid shrink-0 grid-cols-6 gap-1.5 text-center sm:gap-2">
                                                @foreach (range(1, 6) as $column)
                                                    <div class="w-8 sm:w-9">
                                                        <div class="mx-auto h-4 w-4 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-5"></div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if ($this->allHistory->isNotEmpty())
                            <div class="border-t border-border px-5 py-4" data-team-history-controls>
                                <nav class="ui-pagination" aria-label="Team history pagination">
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
