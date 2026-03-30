<section class="ui-section" data-venue-teams-section>
    <div class="ui-shell-grid">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Teams</h3>
            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Active teams currently playing out of this venue in the open season.
            </p>
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
