<div>
    @if ($this->frames->count() > 0)
        <section class="ui-section"
            data-player-frames-section
            @if ($forAccount) data-account-frames-section @endif>
            <div class="ui-shell-grid">
                <div class="ui-section-intro gap-2">
                    <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-list-details size-5 text-neutral-700 dark:text-neutral-200">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M13 5h8" />
                            <path d="M13 9h5" />
                            <path d="M13 15h8" />
                            <path d="M13 19h5" />
                            <path d="M3 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -4" />
                            <path d="M3 15a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -4" />
                        </svg>
                    </span>
                    <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                        <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Frames</h2>
                        <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                            {{ $forAccount ? 'Recent frames you have played this season.' : 'Recent frames this player has played in the current section.' }}
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="ui-card">
                        <div class="ui-averages-item-group"
                            wire:loading.remove
                            wire:target="previousPage, nextPage"
                            data-player-frames-list>
                            @foreach ($this->frameRows as $frameRow)
                                @php
                                    $resultBadgeClasses = $frameRow->won_frame
                                        ? 'ui-live-score-badge-win'
                                        : 'ui-live-score-badge-loss';
                                @endphp

                                <a href="{{ route('result.show', $frameRow->result_id) }}"
                                    class="ui-average-item focus-visible:ring-2 focus-visible:ring-gray-900/20 dark:focus-visible:ring-neutral-100/20"
                                    wire:key="player-frame-{{ $forAccount ? 'account' : 'public' }}-{{ $frameRow->result_id }}-{{ $loop->index }}"
                                    data-slot="item"
                                    data-variant="muted"
                                    data-size="default">
                                    <div class="ui-average-item-content" data-slot="item-content">
                                        <div class="shrink-0">
                                            <span class="ui-fixture-badge {{ $resultBadgeClasses }}" data-slot="badge">
                                                {{ $frameRow->won_frame ? 'W' : 'L' }}
                                            </span>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <p class="truncate whitespace-nowrap text-sm font-semibold text-neutral-950 dark:text-neutral-50">{{ $frameRow->opponent_name }}</p>
                                            <p class="mt-1 truncate whitespace-nowrap text-xs text-neutral-500 dark:text-neutral-400">{{ $frameRow->opponent_team }}</p>
                                        </div>

                                        <time class="shrink-0 text-xs text-neutral-500 dark:text-neutral-400">
                                            {{ $frameRow->fixture_date_label }}
                                        </time>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="animate-pulse" wire:loading.block wire:target="previousPage, nextPage" data-player-frames-loading>
                            <div class="ui-averages-item-group">
                                @foreach (range(1, 5) as $row)
                                    <div class="ui-average-item">
                                        <div class="ui-average-item-content">
                                            <div class="h-5 w-7 shrink-0 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                            <div class="min-w-0 flex-1 space-y-2">
                                                <div class="h-4 w-28 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-36"></div>
                                                <div class="h-3 w-20 rounded-full bg-gray-100 dark:bg-neutral-900/70 sm:w-28"></div>
                                            </div>
                                            <div class="h-3 w-12 shrink-0 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if ($this->frames->hasPages())
                            <div class="border-t border-border px-5 py-4" @if ($forAccount) data-account-frames-controls @else data-player-frames-controls @endif>
                                <nav class="ui-pagination" aria-label="Player frames pagination">
                                    <button wire:click="previousPage"
                                        wire:loading.attr="disabled"
                                        class="ui-pagination-link rounded-full"
                                        aria-label="Previous page"
                                        type="button"
                                        @disabled($this->frames->onFirstPage())>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left size-4" aria-hidden="true">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M15 6l-6 6l6 6" />
                                        </svg>
                                        <span class="hidden sm:inline">Previous</span>
                                    </button>

                                    <span class="ui-pagination-current" aria-live="polite">Page {{ $this->frames->currentPage() }}</span>

                                    <button wire:click="nextPage"
                                        wire:loading.attr="disabled"
                                        class="ui-pagination-link rounded-full"
                                        aria-label="Next page"
                                        type="button"
                                        @disabled(! $this->frames->hasMorePages())>
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
