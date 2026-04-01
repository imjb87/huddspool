@if ($frames && $frames->count() > 0)
    <section class="border-t border-gray-200 pt-6 dark:border-neutral-800/80" data-player-frames-section>
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="ui-section-intro">
                <div class="ui-section-intro-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5m-16.5 6h16.5m-16.5 6h16.5" />
                    </svg>
                </div>
                <div class="ui-section-intro-copy">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Frames</h3>
                    <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                        Recent frames this player has played in the current section.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="divide-y divide-gray-200 dark:divide-neutral-800/80">
                    @foreach ($frameRows as $frameRow)
                        <a href="{{ route('result.show', $frameRow->result_id) }}"
                            class="group block"
                            wire:key="player-frame-{{ $frameRow->result_id }}-{{ $loop->index }}">
                            <div class="flex items-center gap-4 rounded-lg py-4 transition sm:-mx-3 sm:-my-px sm:px-3 group-hover:bg-gray-200/70 dark:group-hover:bg-neutral-900/70">
                                <div class="shrink-0">
                                    <span class="ui-score-pill min-w-[28px] items-center justify-center px-2 {{ $frameRow->result_pill_classes }}">
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

                @if ($frames->hasPages())
                    <div class="pt-5 pb-4 lg:pt-5 lg:pb-6" data-player-frames-controls>
                        <div class="flex items-center justify-between gap-4">
                            @if ($frames->onFirstPage())
                                <span class="inline-flex w-24 items-center justify-center rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-400 dark:border-neutral-800 dark:text-gray-500">
                                    Previous
                                </span>
                            @else
                                <a href="{{ $frames->previousPageUrl() }}"
                                    class="inline-flex w-24 items-center justify-center rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-neutral-800 dark:text-gray-200 dark:hover:bg-neutral-900">
                                    Previous
                                </a>
                            @endif

                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Page {{ $frames->currentPage() }}
                            </span>

                            @if ($frames->hasMorePages())
                                <a href="{{ $frames->nextPageUrl() }}"
                                    class="inline-flex w-24 items-center justify-center rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-neutral-800 dark:text-gray-200 dark:hover:bg-neutral-900">
                                    Next
                                </a>
                            @else
                                <span class="inline-flex w-24 items-center justify-center rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-400 dark:border-neutral-800 dark:text-gray-500">
                                    Next
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif
