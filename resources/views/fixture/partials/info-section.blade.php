<section class="ui-section" data-fixture-info-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro gap-2">
            <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar size-5 text-neutral-700 dark:text-neutral-200">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12" />
                    <path d="M16 3v4" />
                    <path d="M8 3v4" />
                    <path d="M4 11h16" />
                    <path d="M11 15h1" />
                    <path d="M12 15v3" />
                </svg>
            </span>
            <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Fixture information</h2>
                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                    Match details, venue, and links back to the wider section schedule.
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
                                <a href="{{ route('team.show', $fixture->homeTeam) }}" class="ui-link inline-flex text-sm font-semibold">
                                    {{ $fixture->homeTeam->name }}
                                </a>
                                <span class="font-normal text-neutral-400 dark:text-neutral-500">vs</span>
                                <a href="{{ route('team.show', $fixture->awayTeam) }}" class="ui-link inline-flex text-sm font-semibold">
                                    {{ $fixture->awayTeam->name }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Date</p>
                            <p class="text-sm text-neutral-950 dark:text-neutral-50">{{ $fixture->fixture_date->format('l jS F Y') }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Ruleset</p>
                            <a href="{{ route('ruleset.section.show', ['ruleset' => $fixture->section->ruleset, 'section' => $fixture->section, 'tab' => 'fixtures-results']) }}"
                                class="ui-link inline-flex text-sm font-semibold">
                                {{ $fixture->section->ruleset->name }}
                            </a>
                        </div>

                        <div class="sm:col-span-2">
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Venue</p>
                            @if ($fixture->venue)
                                <a href="{{ route('venue.show', $fixture->venue) }}"
                                    class="ui-link inline-flex text-sm font-semibold">
                                    {{ $fixture->venue->name }}
                                </a>
                            @else
                                <p class="text-sm text-muted-foreground">Venue TBC</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
