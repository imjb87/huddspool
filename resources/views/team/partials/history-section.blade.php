@if ($history->isNotEmpty())
    <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-team-history-section>
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">History</h3>
                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Season-by-season record for this team across previous campaigns.
                </p>
            </div>

            <div class="lg:col-span-2">
                <div class="flex items-center justify-between gap-2 pb-0.5 sm:px-3">
                    <div class="flex min-w-0 items-center gap-2 sm:gap-3"></div>

                    <div class="ml-auto grid shrink-0 grid-cols-5 gap-1.5 text-center sm:gap-2">
                        <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-9">Pl</div>
                        <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-9">W</div>
                        <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-9">D</div>
                        <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-9">L</div>
                        <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-9">Pts</div>
                    </div>
                </div>

                <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                    @foreach ($historyRows as $historyRow)
                        <div wire:key="team-history-{{ $historyRow->season_id }}-{{ $historyRow->ruleset_id }}">
                            @if ($historyRow->history_link)
                                <a href="{{ $historyRow->history_link }}" class="block py-4 transition hover:bg-gray-200/70 dark:hover:bg-zinc-800/70 sm:-mx-3 sm:-my-px sm:rounded-xl sm:px-3">
                            @endif
                            <div class="flex items-center {{ $historyRow->history_link ? '' : 'sm:rounded-xl sm:px-3' }}">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $historyRow->season_name }}</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $historyRow->ruleset_name }}</p>
                                </div>

                                <div class="ml-auto grid shrink-0 grid-cols-5 gap-1.5 text-center sm:gap-2">
                                    <div class="w-8 sm:w-9">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $historyRow->played }}</p>
                                    </div>
                                    <div class="w-8 sm:w-9">
                                        <p class="text-sm font-semibold text-green-700 dark:text-green-400">{{ $historyRow->wins }}</p>
                                    </div>
                                    <div class="w-8 sm:w-9">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $historyRow->draws }}</p>
                                    </div>
                                    <div class="w-8 sm:w-9">
                                        <p class="text-sm font-semibold text-red-700 dark:text-red-400">{{ $historyRow->losses }}</p>
                                    </div>
                                    <div class="w-8 sm:w-9">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $historyRow->points }}</p>
                                    </div>
                                </div>
                            </div>
                            @if ($historyRow->history_link)
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
