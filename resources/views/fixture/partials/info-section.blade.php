<section class="ui-section" data-fixture-info-section>
    <div class="ui-shell-grid">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Fixture information</h3>
            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Match details, venue, and links back to the wider section schedule.
            </p>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card">
                <div class="ui-card-body">
                    <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Match</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                <a href="{{ route('team.show', $fixture->homeTeam) }}"
                                    class="ui-link inline-flex text-sm font-semibold">
                                    {{ $fixture->homeTeam->name }}
                                </a>
                                <span class="font-normal text-gray-400 dark:text-zinc-500">vs</span>
                                <a href="{{ route('team.show', $fixture->awayTeam) }}"
                                    class="ui-link inline-flex text-sm font-semibold">
                                    {{ $fixture->awayTeam->name }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Date</p>
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $fixture->fixture_date->format('l jS F Y') }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Ruleset</p>
                            <a href="{{ route('ruleset.section.show', ['ruleset' => $fixture->section->ruleset, 'section' => $fixture->section, 'tab' => 'fixtures-results']) }}"
                                class="ui-link inline-flex text-sm font-semibold">
                                {{ $fixture->section->ruleset->name }}
                            </a>
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
