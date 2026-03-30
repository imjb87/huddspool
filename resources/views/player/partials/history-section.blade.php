@if ($history->isNotEmpty())
    <section class="ui-section" data-player-history-section>
        <div class="ui-shell-grid">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">History</h3>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Season-by-season playing history with archived team and section details.
                </p>
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

                    <div class="ui-card-rows">
                    @foreach ($history as $entry)
                        <div class="ui-card-row items-center px-4 sm:px-5" wire:key="player-history-{{ $entry['season_id'] }}-{{ $entry['section_id'] }}-{{ $loop->index }}">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $entry['season_name'] }}</p>
                                <p class="mt-1 truncate text-sm text-gray-700 dark:text-gray-300">{{ $entry['team_name'] ?? 'Team TBC' }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $entry['section_name'] ?? 'Section TBC' }}</p>
                            </div>

                            <div class="ml-auto flex shrink-0 items-start gap-2 self-center text-center">
                                <div class="w-16">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Played</p>
                                    <p class="mt-1 text-sm font-semibold leading-5 text-gray-900 dark:text-gray-100">{{ $entry['played'] }}</p>
                                    <span class="mt-1 inline-flex h-[18px] items-center text-[10px] font-semibold text-transparent">0%</span>
                                </div>
                                <div class="w-16">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Won</p>
                                    <p class="mt-1 text-sm font-semibold leading-5 text-green-700 dark:text-green-400">{{ $entry['wins'] }}</p>
                                    <span class="mt-1 inline-flex items-center rounded-md bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-500/15 dark:text-green-300">{{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($entry['win_percentage']) }}%</span>
                                </div>
                                <div class="w-16">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Lost</p>
                                    <p class="mt-1 text-sm font-semibold leading-5 text-red-700 dark:text-red-400">{{ $entry['losses'] }}</p>
                                    <span class="mt-1 inline-flex items-center rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-500/15 dark:text-red-300">{{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($entry['loss_percentage']) }}%</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
