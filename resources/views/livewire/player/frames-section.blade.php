<div>
    @if ($this->frames->count() > 0)
        <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80"
            data-player-frames-section
            @if ($forAccount) data-account-frames-section @endif>
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Frames</h3>
                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    {{ $forAccount ? 'Recent frames you have played this season.' : 'Recent frames this player has played in the current section.' }}
                </p>
            </div>

            <div class="lg:col-span-2">
                <div class="divide-y divide-gray-200 dark:divide-zinc-800/80"
                    wire:loading.remove
                    wire:target="previousPage, nextPage">
                    @foreach ($this->frameRows as $frameRow)
                        <a href="{{ route('result.show', $frameRow->result_id) }}"
                            class="group block"
                            wire:key="player-frame-{{ $forAccount ? 'account' : 'public' }}-{{ $frameRow->result_id }}-{{ $loop->index }}">
                            <div class="flex items-center gap-4 rounded-lg py-4 transition sm:-mx-3 sm:-my-px sm:px-3 group-hover:bg-gray-200/70 dark:group-hover:bg-zinc-800/70">
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
                    <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                        @foreach (range(1, 5) as $row)
                            <div class="flex items-center gap-4 rounded-lg py-4 sm:-mx-3 sm:-my-px sm:px-3">
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

                @if ($this->frames->hasPages())
                    <div class="pt-5 pb-4 lg:pt-5 lg:pb-6" @if ($forAccount) data-account-frames-controls @else data-player-frames-controls @endif>
                        <div class="flex items-center justify-between gap-4">
                            <button wire:click="previousPage"
                                wire:loading.attr="disabled"
                                class="inline-flex w-24 cursor-pointer items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-3 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                                aria-label="Previous"
                                {{ $this->frames->onFirstPage() ? 'disabled' : '' }}>
                                Previous
                            </button>

                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                Page {{ $this->frames->currentPage() }}
                            </span>

                            <button wire:click="nextPage"
                                wire:loading.attr="disabled"
                                class="inline-flex w-24 cursor-pointer items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-3 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                                aria-label="Next"
                                {{ $this->frames->hasMorePages() ? '' : 'disabled' }}>
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
