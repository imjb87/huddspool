<section class="ui-section" data-venue-teams-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro">
            <div class="ui-section-intro-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.742-.478 3 3 0 0 0-4.682-2.72m.94 3.198v-.75A2.25 2.25 0 0 0 15.75 15.72h-7.5A2.25 2.25 0 0 0 6 17.97v.75m12 0a9.094 9.094 0 0 1-12 0m12 0a9.094 9.094 0 0 0-12 0m8.25-10.47a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
            </div>
            <div class="ui-section-intro-copy">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Teams</h3>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Active teams currently playing out of this venue in the open season.
                </p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card">
                <div class="ui-card-rows" data-venue-teams-list>
                    @forelse ($venueTeams as $teamRow)
                        <div wire:key="venue-team-{{ $teamRow['team']->id }}">
                            <a href="{{ route('team.show', $teamRow['team']) }}"
                                class="ui-card-row-link">
                                <div class="ui-card-row items-start px-4 sm:px-5">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $teamRow['team']->name }}</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            {{ $teamRow['section_name'] }}
                                        </p>
                                    </div>

                                    @if ($teamRow['captain_name'])
                                        <div class="shrink-0 text-right">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Captain</p>
                                            <p class="mt-1 text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $teamRow['captain_name'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="ui-card-body">
                            <p class="text-sm text-gray-500 dark:text-gray-400">No active teams for the current season.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
