<section class="py-1" data-team-info-section>
    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Team information</h3>
            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Current details for this team in the open season, including section, standing, venue, and captain.
            </p>
        </div>

        <div class="lg:col-span-2">
            <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $team->name }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Section</p>
                    @if ($section)
                        <a href="{{ route('ruleset.section.show', ['ruleset' => $section->ruleset, 'section' => $section]) }}"
                            class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                            {{ $section->name }}
                        </a>
                    @else
                        <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">No open section</p>
                    @endif
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Venue</p>
                    @if ($team->venue)
                        <a href="{{ route('venue.show', $team->venue) }}"
                            class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                            {{ $team->venue->name }}
                        </a>
                    @else
                        <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">Venue TBC</p>
                    @endif
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Captain</p>
                    @if ($team->captain)
                        <a href="{{ route('player.show', $team->captain) }}"
                            class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                            {{ $team->captain->name }}
                        </a>
                    @else
                        <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">Captain TBC</p>
                    @endif
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Current standing</p>
                    @if ($currentStanding)
                        <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">
                            {{ $currentStanding->label }}
                            <span class="text-gray-500 dark:text-gray-400">· {{ $currentStanding->points }} pts from {{ $currentStanding->played }} played</span>
                        </p>
                    @else
                        <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">No standing available yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
