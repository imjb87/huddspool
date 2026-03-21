<section class="py-1" data-result-info-section>
    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Result information</h3>
            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Match details, venue, and links back to the wider section schedule.
            </p>
        </div>

        <div class="space-y-5 lg:col-span-2">
            <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Match</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        @if ($fixture->homeTeam)
                            <a href="{{ route('team.show', $fixture->homeTeam) }}"
                                class="inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                                {{ $result->home_team_name }}
                            </a>
                        @else
                            {{ $result->home_team_name }}
                        @endif
                        <span class="font-normal text-gray-400 dark:text-zinc-500">vs</span>
                        @if ($fixture->awayTeam)
                            <a href="{{ route('team.show', $fixture->awayTeam) }}"
                                class="inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                                {{ $result->away_team_name }}
                            </a>
                        @else
                            {{ $result->away_team_name }}
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</p>
                    <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">
                        {{ $result->fixture->fixture_date->format('l jS F Y') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ruleset</p>
                    @if ($sectionLink && $ruleset)
                        <a href="{{ $sectionLink }}"
                            class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                            {{ $ruleset->name }}
                        </a>
                    @else
                        <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">Unavailable</p>
                    @endif
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Venue</p>
                    @if ($fixture->venue)
                        <a href="{{ route('venue.show', $fixture->venue) }}"
                            class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                            {{ $fixture->venue->name }}
                        </a>
                    @else
                        <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">Venue TBC</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
