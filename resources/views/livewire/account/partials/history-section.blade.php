@if ($this->history->isNotEmpty())
    <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-account-history-section>
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">History</h3>
                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Season-by-season playing record using the archived team and section details from recorded results.
                </p>
            </div>

            <div class="lg:col-span-2">
                <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                    @foreach ($this->history as $entry)
                        <div class="flex items-center gap-4 py-4" wire:key="account-history-{{ $entry['season_id'] }}-{{ $entry['section_id'] }}-{{ $loop->index }}">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $entry['season_name'] }}</p>
                                <p class="mt-1 truncate text-sm text-gray-700 dark:text-gray-300">{{ $entry['team_name'] ?? 'Team TBC' }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $entry['section_name'] ?? 'Section TBC' }}</p>
                            </div>

                            <div class="ml-auto flex shrink-0 items-start gap-3 self-center text-center">
                                <div class="w-16">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Played</p>
                                    <p class="mt-1 text-sm font-semibold leading-5 text-gray-900 dark:text-gray-100">{{ $entry['played'] }}</p>
                                    <span class="mt-1 inline-flex h-[18px] items-center text-[10px] font-semibold text-transparent">
                                        0%
                                    </span>
                                </div>
                                <div class="w-16">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Won</p>
                                    <p class="mt-1 text-sm font-semibold leading-5 text-green-700 dark:text-green-400">{{ $entry['wins'] }}</p>
                                    <span class="mt-1 inline-flex items-center rounded-md bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-950/50 dark:text-green-300">{{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($entry['win_percentage']) }}%</span>
                                </div>
                                <div class="w-16">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Lost</p>
                                    <p class="mt-1 text-sm font-semibold leading-5 text-red-700 dark:text-red-400">{{ $entry['losses'] }}</p>
                                    <span class="mt-1 inline-flex items-center rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-950/50 dark:text-red-300">{{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($entry['loss_percentage']) }}%</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
