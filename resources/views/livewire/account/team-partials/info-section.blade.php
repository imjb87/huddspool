<section class="ui-section" data-account-team-info-section>
    <div class="ui-shell-grid">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Team information</h3>
            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Current team details for the open season, including your section and standing.
            </p>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card">
                <div class="ui-card-body">
                    <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Name</p>
                            <a href="{{ route('team.show', $this->team) }}" class="ui-link inline-flex text-sm font-semibold">
                                {{ $this->team->name }}
                            </a>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Section</p>
                            @if ($this->currentSection)
                                <a href="{{ route('ruleset.section.show', ['ruleset' => $this->currentSection->ruleset, 'section' => $this->currentSection]) }}"
                                    class="ui-link inline-flex text-sm font-semibold">
                                    {{ $this->currentSection->name }}
                                </a>
                            @else
                                <p class="text-sm text-gray-900 dark:text-gray-100">No open section</p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Venue</p>
                            @if ($this->team->venue)
                                <a href="{{ route('venue.show', $this->team->venue) }}"
                                    class="ui-link inline-flex text-sm font-semibold">
                                    {{ $this->team->venue->name }}
                                </a>
                            @else
                                <p class="text-sm text-gray-900 dark:text-gray-100">Venue TBC</p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Captain</p>
                            @if ($this->team->captain)
                                <a href="{{ route('player.show', $this->team->captain) }}"
                                    class="ui-link inline-flex text-sm font-semibold">
                                    {{ $this->team->captain->name }}
                                </a>
                            @else
                                <p class="text-sm text-gray-900 dark:text-gray-100">Captain TBC</p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Current standing</p>
                            @if ($this->currentStanding)
                                <p class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $this->currentStanding->label }}
                                    <span class="text-gray-500 dark:text-gray-400">· {{ $this->currentStanding->points }} pts from {{ $this->currentStanding->played }} played</span>
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
