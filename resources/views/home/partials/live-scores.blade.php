<section class="ui-section" data-home-live-scores>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-shell-grid">
            <div>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Live scores</h2>
                <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">
                    Results currently being added across the league.
                </p>
            </div>
            <div class="lg:col-span-2">
                @if ($liveScores->isEmpty())
                    <div class="ui-card">
                        <div class="ui-card-body px-6 py-10 text-center">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">No current matches in progress right now.</p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Check back during league night to follow the latest scores as they come in.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="ui-card" data-home-live-scores-shell>
                        <div class="ui-card-rows max-h-80 overflow-y-auto overscroll-contain" data-home-live-scores-list>
                            @foreach ($liveScores as $result)
                                <div data-home-live-score-row>
                                    <a href="{{ $result->live_score_url }}" class="ui-card-row-link">
                                        <div class="ui-card-row items-start">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 sm:hidden">
                                                {{ $result->home_team_shortname ?: $result->home_team_name }} <span class="font-normal text-gray-400 dark:text-gray-500">vs</span> {{ $result->away_team_shortname ?: $result->away_team_name }}
                                            </p>
                                            <p class="hidden text-sm font-semibold text-gray-900 dark:text-gray-100 sm:block">
                                                {{ $result->home_team_name }} <span class="font-normal text-gray-400 dark:text-gray-500">vs</span> {{ $result->away_team_name }}
                                            </p>
                                            @if ($result->row_meta !== '')
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $result->row_meta }}</p>
                                            @endif
                                        </div>

                                            <div class="ml-auto flex shrink-0 self-center items-center text-right">
                                                <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10"
                                                    data-home-live-score-pill>
                                                    <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">
                                                        {{ $result->home_score }}
                                                    </div>
                                                    <div class="w-px bg-white/25"></div>
                                                    <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">
                                                        {{ $result->away_score }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
