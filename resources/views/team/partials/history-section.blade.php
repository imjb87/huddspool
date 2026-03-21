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
                <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                    @foreach ($historyRows as $historyRow)
                        <div wire:key="team-history-{{ $historyRow->season_id }}-{{ $historyRow->ruleset_id }}">
                            @if ($historyRow->history_link)
                                <a href="{{ $historyRow->history_link }}" class="block rounded-lg transition">
                            @endif
                            <div class="flex items-center gap-4 py-4">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $historyRow->season_name }}</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $historyRow->ruleset_name }}</p>
                                </div>

                                <div class="ml-auto flex shrink-0 items-center gap-2 text-center">
                                    <div class="w-11">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Pl</p>
                                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $historyRow->played }}</p>
                                    </div>
                                    <div class="w-11">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">W</p>
                                        <p class="mt-1 text-sm font-semibold text-green-700 dark:text-green-400">{{ $historyRow->wins }}</p>
                                    </div>
                                    <div class="w-11">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">D</p>
                                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $historyRow->draws }}</p>
                                    </div>
                                    <div class="w-11">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">L</p>
                                        <p class="mt-1 text-sm font-semibold text-red-700 dark:text-red-400">{{ $historyRow->losses }}</p>
                                    </div>
                                    <div class="w-11">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Pts</p>
                                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $historyRow->points }}</p>
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
