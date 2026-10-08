<section class="ui-section" data-team-info-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro gap-2">
            <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-building size-5 text-neutral-700 dark:text-neutral-200">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M3 21l18 0" />
                    <path d="M5 21v-14a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v14" />
                    <path d="M9 9l1 0" />
                    <path d="M9 13l1 0" />
                    <path d="M14 9l1 0" />
                    <path d="M14 13l1 0" />
                </svg>
            </span>
            <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Team information</h2>
                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                    Current details for this team in the open season, including section, standing, venue, and captain.
                </p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card">
                <div class="ui-card-body">
                    <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Name</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $team->name }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Section</p>
                            @if ($section)
                                <a href="{{ route('ruleset.section.show', ['ruleset' => $section->ruleset, 'section' => $section]) }}"
                                    class="ui-link inline-flex">
                                    {{ $section->name }}
                                </a>
                            @else
                                <p class="text-sm text-muted-foreground">No open section</p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Venue</p>
                            @if ($team->venue)
                                <a href="{{ route('venue.show', $team->venue) }}"
                                    class="ui-link inline-flex">
                                    {{ $team->venue->name }}
                                </a>
                            @else
                                <p class="text-sm text-muted-foreground">Venue TBC</p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Captain</p>
                            @if ($team->captain)
                                <a href="{{ route('player.show', $team->captain) }}"
                                    class="ui-link inline-flex">
                                    {{ $team->captain->name }}
                                </a>
                            @else
                                <p class="text-sm text-muted-foreground">Captain TBC</p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Current standing</p>
                            @if ($currentStanding)
                                <p class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $currentStanding->label }}
                                    <span class="text-gray-500 dark:text-gray-400">· {{ $currentStanding->points }} pts from {{ $currentStanding->played }} played</span>
                                </p>
                            @else
                                <p class="text-sm text-muted-foreground">No standing available yet</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
