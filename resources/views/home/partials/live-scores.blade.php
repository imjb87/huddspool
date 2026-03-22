<section class="bg-white py-8 dark:bg-zinc-900 sm:py-10" data-home-live-scores>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Live scores</h2>
                <p class="text-sm leading-6 text-gray-600 dark:text-gray-400">
                    Results currently being added across the league.
                </p>
            </div>
            <div class="lg:col-span-2">
                @if ($liveScores->isEmpty())
                    <div class="rounded-[1.75rem] border border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center dark:border-zinc-700 dark:bg-zinc-900/70">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">No current matches in progress right now.</p>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Check back during league night to follow the latest scores as they come in.
                        </p>
                    </div>
                @else
                    <div class="max-h-80 divide-y divide-gray-200 overflow-y-auto overscroll-contain dark:divide-zinc-800/80"
                        data-home-live-scores-shell
                        data-home-live-scores-list>
                        @foreach ($liveScores as $result)
                            <div data-home-live-score-row>
                                <a href="{{ route('result.show', $result) }}"
                                    class="block rounded-xl sm:px-3 py-4 transition hover:bg-gray-200/70 dark:hover:bg-zinc-800/70">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
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
                @endif
            </div>
        </div>
    </div>
</section>
