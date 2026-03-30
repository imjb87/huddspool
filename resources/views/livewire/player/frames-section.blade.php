<div>
    @if ($this->frames->count() > 0)
        <section class="ui-section"
            data-player-frames-section
            @if ($forAccount) data-account-frames-section @endif>
        <div class="ui-shell-grid">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Frames</h3>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    {{ $forAccount ? 'Recent frames you have played this season.' : 'Recent frames this player has played in the current section.' }}
                </p>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card">
                    <div class="ui-card-rows"
                        wire:loading.remove
                        wire:target="previousPage, nextPage">
                        @foreach ($this->frameRows as $frameRow)
                            <a href="{{ route('result.show', $frameRow->result_id) }}"
                                class="ui-card-row-link"
                                wire:key="player-frame-{{ $forAccount ? 'account' : 'public' }}-{{ $frameRow->result_id }}-{{ $loop->index }}">
                                <div class="ui-card-row items-center px-4 sm:px-5">
                                    <div class="shrink-0">
                                        <span class="inline-flex h-7 min-w-[28px] items-center justify-center rounded-full px-2 text-xs font-bold text-white shadow-sm ring-1 ring-black/10 {{ $frameRow->result_pill_classes }}">
                                            {{ $frameRow->won_frame ? 'W' : 'L' }}
                                        </span>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $frameRow->opponent_name }}</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $frameRow->opponent_team }}</p>
                                    </div>

                                    <div class="shrink-0 text-right text-sm text-gray-500 dark:text-gray-400">
                                        {{ $frameRow->fixture_date_label }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="animate-pulse"
                        wire:loading.block
                        wire:target="previousPage, nextPage"
                        data-player-frames-loading>
                        <div class="ui-card-rows">
                            @foreach (range(1, 5) as $row)
                                <div class="ui-card-row items-center px-4 sm:px-5">
                                    <div class="h-7 w-7 shrink-0 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                    <div class="min-w-0 flex-1 space-y-2">
                                        <div class="h-4 w-28 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-36"></div>
                                        <div class="h-3 w-20 rounded-full bg-gray-100 dark:bg-zinc-800/70 sm:w-28"></div>
                                    </div>
                                    <div class="h-4 w-20 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if ($this->frames->hasPages())
                    <div class="pt-5 pb-4 lg:pt-5 lg:pb-6" @if ($forAccount) data-account-frames-controls @else data-player-frames-controls @endif>
                        <div class="flex items-center justify-between gap-4">
                            <button wire:click="previousPage"
                                wire:loading.attr="disabled"
                                class="ui-button-primary min-w-24 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0"
                                aria-label="Previous"
                                @disabled($this->frames->onFirstPage())>
                                Previous
                            </button>

                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                Page {{ $this->frames->currentPage() }}
                            </span>

                            <button wire:click="nextPage"
                                wire:loading.attr="disabled"
                                class="ui-button-primary min-w-24 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0"
                                aria-label="Next"
                                @disabled(! $this->frames->hasMorePages())>
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
