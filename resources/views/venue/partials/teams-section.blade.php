<section class="ui-section" data-venue-teams-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro gap-2">
            <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users-group size-5 text-neutral-700 dark:text-neutral-200">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    <path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" />
                    <path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    <path d="M17 10h2a2 2 0 0 1 2 2v1" />
                    <path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    <path d="M3 13v-1a2 2 0 0 1 2 -2h2" />
                </svg>
            </span>
            <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Teams</h2>
                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                    See which teams are based at this venue this season.
                </p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card ui-section-see-also-card">
                <div class="ui-section-see-also-list" data-venue-teams-list data-slot="item-group">
                    @forelse ($venueTeams as $teamRow)
                        <a href="{{ route('team.show', $teamRow['team']) }}"
                            class="ui-section-see-also-item"
                            wire:key="venue-team-{{ $teamRow['team']->id }}"
                            data-slot="item"
                            data-variant="muted"
                            data-size="default">
                            <div class="ui-section-see-also-item-content" data-slot="item-content">
                                <p class="ui-section-see-also-item-title" data-slot="item-title">
                                    {{ $teamRow['team']->name }}
                                </p>
                                <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-neutral-500 dark:text-neutral-400">
                                    <span>{{ $teamRow['section_name'] }}</span>
                                    @if ($teamRow['captain_name'])
                                        <span aria-hidden="true">·</span>
                                        <span>Captain {{ $teamRow['captain_name'] }}</span>
                                    @endif
                                </div>
                            </div>
                            <span class="ui-section-see-also-item-action" data-slot="item-actions" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right size-4">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M9 6l6 6l-6 6" />
                                </svg>
                            </span>
                        </a>
                    @empty
                        <x-ui-empty-state
                            title="No active teams for the current season."
                            data-venue-teams-empty
                        />
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
