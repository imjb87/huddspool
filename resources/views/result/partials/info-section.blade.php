<section class="ui-section" data-result-info-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro">
            <div class="ui-section-intro-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25 12 11.25v4.5h.75m-3-9h4.5m6 3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div class="ui-section-intro-copy">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Result information</h3>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Match details, venue, and links back to the wider section schedule.
                </p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card">
                <div class="ui-card-body">
                    <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Match</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        @if ($fixture->homeTeam)
                            <a href="{{ route('team.show', $fixture->homeTeam) }}"
                                class="ui-link inline-flex text-sm font-semibold">
                                {{ $result->home_team_name }}
                            </a>
                        @else
                            {{ $result->home_team_name }}
                        @endif
                        <span class="font-normal text-gray-400 dark:text-neutral-500">vs</span>
                        @if ($fixture->awayTeam)
                            <a href="{{ route('team.show', $fixture->awayTeam) }}"
                                class="ui-link inline-flex text-sm font-semibold">
                                {{ $result->away_team_name }}
                            </a>
                        @else
                            {{ $result->away_team_name }}
                        @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Date</p>
                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $result->fixture->fixture_date->format('l jS F Y') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Ruleset</p>
                            @if ($sectionLink && $ruleset)
                                <a href="{{ $sectionLink }}"
                                    class="ui-link inline-flex text-sm font-semibold">
                                    {{ $ruleset->name }}
                                </a>
                            @else
                                <p class="text-sm text-gray-900 dark:text-gray-100">Unavailable</p>
                            @endif
                        </div>

                        <div class="sm:col-span-2">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Venue</p>
                            @if ($fixture->venue)
                                <a href="{{ route('venue.show', $fixture->venue) }}"
                                    class="ui-link inline-flex text-sm font-semibold">
                                    {{ $fixture->venue->name }}
                                </a>
                            @else
                                <p class="text-sm text-gray-900 dark:text-gray-100">Venue TBC</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
