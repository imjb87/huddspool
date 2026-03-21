<section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-venue-teams-section>
    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Teams</h3>
            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Active teams currently playing out of this venue in the open season.
            </p>
        </div>

        <div class="lg:col-span-2">
            <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                @forelse ($venueTeams as $teamRow)
                    <a href="{{ route('team.show', $teamRow['team']) }}"
                        class="block rounded-xl px-3 py-4 transition hover:bg-gray-50 dark:hover:bg-zinc-800/70"
                        wire:key="venue-team-{{ $teamRow['team']->id }}">
                        <div class="flex items-start justify-between gap-4">
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
                @empty
                    <div class="py-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No active teams for the current season.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
