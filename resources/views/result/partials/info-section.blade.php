<section class="ui-section" data-result-info-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro gap-2">
            <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle size-5 text-neutral-700 dark:text-neutral-200">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                    <path d="M12 9h.01" />
                    <path d="M11 12h1v4h1" />
                </svg>
            </span>
            <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Result information</h2>
                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                    Confirm the teams, venue, and date before reviewing the scorecard.
                </p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card">
                <div class="ui-card-body">
                    <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Match</p>
                            <p class="flex flex-wrap items-center gap-x-1 text-sm font-semibold text-neutral-950 dark:text-neutral-50">
                                @if ($fixture->homeTeam)
                                    <a href="{{ route('team.show', $fixture->homeTeam) }}" class="ui-link inline-flex text-sm font-semibold">
                                        {{ $result->home_team_name }}
                                    </a>
                                @else
                                    {{ $result->home_team_name }}
                                @endif
                                <span class="font-normal text-neutral-400 dark:text-neutral-500">vs</span>
                                @if ($fixture->awayTeam)
                                    <a href="{{ route('team.show', $fixture->awayTeam) }}" class="ui-link inline-flex text-sm font-semibold">
                                        {{ $result->away_team_name }}
                                    </a>
                                @else
                                    {{ $result->away_team_name }}
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Date</p>
                            <p class="text-sm text-neutral-950 dark:text-neutral-50">
                                {{ $result->fixture->fixture_date->format('l jS F Y') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Ruleset</p>
                            @if ($sectionLink && $ruleset)
                                <a href="{{ $sectionLink }}"
                                    class="ui-link inline-flex text-sm font-semibold">
                                    {{ $ruleset->name }}
                                </a>
                            @else
                                <p class="text-sm text-muted-foreground">Ruleset not available</p>
                            @endif
                        </div>

                        <div class="sm:col-span-2">
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Venue</p>
                            @if ($fixture->venue)
                                <a href="{{ route('venue.show', $fixture->venue) }}"
                                    class="ui-link inline-flex text-sm font-semibold">
                                    {{ $fixture->venue->name }}
                                </a>
                            @else
                                <p class="text-sm text-muted-foreground">Venue not set</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
