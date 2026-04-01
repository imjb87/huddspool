<section class="ui-section" data-team-info-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro">
            <div class="ui-section-intro-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 20.25h16.5M4.5 3.75h15a.75.75 0 0 1 .75.75v15.75H3.75V4.5a.75.75 0 0 1 .75-.75ZM8.25 8.25h7.5m-7.5 3h7.5m-7.5 3h4.5" />
                </svg>
            </div>
            <div class="ui-section-intro-copy">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Team information</h3>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
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
                                <p class="text-sm text-gray-900 dark:text-gray-100">No open section</p>
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
                                <p class="text-sm text-gray-900 dark:text-gray-100">Venue TBC</p>
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
                                <p class="text-sm text-gray-900 dark:text-gray-100">Captain TBC</p>
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
                                <p class="text-sm text-gray-900 dark:text-gray-100">No standing available yet</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
