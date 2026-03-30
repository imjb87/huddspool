@if ($history->isNotEmpty())
    <section class="ui-section" data-team-history-section>
        <div class="ui-shell-grid">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">History</h3>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Season-by-season record for this team across previous campaigns.
                </p>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card">
                    <div class="ui-card-column-headings px-4 sm:px-5">
                        <div class="flex min-w-0 items-center gap-2 sm:gap-3"></div>

                        <div class="ml-auto grid shrink-0 grid-cols-5 gap-1.5 text-center sm:gap-2">
                            <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-9">Pl</div>
                            <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-9">W</div>
                            <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-9">D</div>
                            <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-9">L</div>
                            <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-9">Pts</div>
                        </div>
                    </div>

                    <div class="ui-card-rows">
                        @foreach ($historyRows as $historyRow)
                            <div wire:key="team-history-{{ $historyRow->season_id }}-{{ $historyRow->ruleset_id }}">
                                @if ($historyRow->history_link)
                                    <a href="{{ $historyRow->history_link }}" class="ui-card-row-link">
                                @endif
                                <div class="ui-card-row px-4 sm:px-5">
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
        </div>
    </section>
@endif
