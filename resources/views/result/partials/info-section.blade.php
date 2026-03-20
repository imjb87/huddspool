<section class="py-1" data-result-info-section>
    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Result information</h3>
            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Match details, venue, and links back to the wider section schedule.
            </p>
        </div>

        <div class="space-y-5 lg:col-span-2">
            @if (! $result->is_confirmed)
                @can('resumeSubmission', $result)
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 dark:border-red-900/60 dark:bg-red-950/40">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-red-700 dark:text-red-300">Result submission</p>
                            <p class="text-sm text-red-900 dark:text-red-100">
                                {{ $result->home_team_name }} vs {{ $result->away_team_name }}
                            </p>
                        </div>

                        <a href="{{ route('result.create', $result->fixture_id) }}"
                            class="inline-flex items-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-4 py-2 text-sm font-semibold text-white shadow-sm ring-1 ring-black/10 transition hover:brightness-110">
                            Continue submitting result
                        </a>
                    </div>
                @endcan
            @endif

            <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Match</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $result->home_team_name }}
                        <span class="font-normal text-gray-400 dark:text-zinc-500">vs</span>
                        {{ $result->away_team_name }}
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
