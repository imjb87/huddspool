<section class="ui-section" data-fixture-head-to-head-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro gap-2">
            <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrows-exchange size-5 text-neutral-700 dark:text-neutral-200">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M7 10h14l-4 -4" />
                    <path d="M17 14h-14l4 4" />
                </svg>
            </span>
            <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Head to head</h2>
                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                    Compare the two teams' current standings before the fixture.
                </p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card ui-standings-card" data-fixture-head-to-head-shell>
                <div class="ui-standings-item-group" data-slot="item-group">
                    <div class="ui-standings-item ui-standings-item-header" data-slot="item" data-variant="muted" data-size="default">
                        <div class="ui-standings-item-content" data-slot="item-content">
                            <div class="flex min-w-0 items-center gap-2 sm:flex-1 sm:gap-3">
                                <div class="ui-card-column-header w-5 text-center sm:w-7">Pos</div>
                            </div>

                            <div class="ui-standings-item-stats" data-slot="item-actions">
                                <div class="ui-card-column-header w-8 sm:w-10">Pl</div>
                                <div class="ui-card-column-header w-8 sm:w-10">W</div>
                                <div class="ui-card-column-header w-8 sm:w-10">D</div>
                                <div class="ui-card-column-header hidden w-8 sm:block sm:w-10">L</div>
                                <div class="ui-card-column-header w-8 sm:w-10">Pts</div>
                            </div>
                        </div>
                    </div>

                    @foreach ($standings as $standing)
                        @php
                            $rowAccentClass = $loop->first
                                ? 'bg-emerald-500 dark:bg-emerald-400'
                                : ($loop->last && $standings->count() > 1 ? 'bg-rose-500 dark:bg-rose-400' : null);
                        @endphp
                        <a href="{{ route('team.show', $standing->id) }}"
                            class="ui-standings-item relative group"
                            wire:key="fixture-standing-{{ $standing->id }}"
                            data-fixture-head-to-head-row
                            data-slot="item"
                            data-variant="muted"
                            data-size="default">
                            @if ($rowAccentClass)
                                <span aria-hidden="true" class="absolute inset-y-2 left-0 w-1 rounded-r-full {{ $rowAccentClass }}"></span>
                            @endif

                            <div class="ui-standings-item-content" data-slot="item-content">
                                <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">
                                    <div class="w-5 shrink-0 text-center text-sm font-semibold tabular-nums text-muted-foreground sm:w-7">
                                        {{ $standing->position }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate whitespace-nowrap text-sm font-semibold text-neutral-950 dark:text-neutral-50">
                                            <span class="sm:hidden">{{ $standing->shortname ?: $standing->name }}</span>
                                            <span class="hidden sm:inline">{{ $standing->name }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="ui-standings-item-stats" data-slot="item-actions">
                                    <div class="w-8 sm:w-10"><p class="text-sm font-semibold text-neutral-950 dark:text-neutral-50">{{ $standing->played }}</p></div>
                                    <div class="w-8 sm:w-10"><p class="text-sm font-semibold text-green-700 dark:text-green-400">{{ $standing->wins }}</p></div>
                                    <div class="w-8 sm:w-10"><p class="text-sm font-semibold text-neutral-950 dark:text-neutral-50">{{ $standing->draws }}</p></div>
                                    <div class="hidden w-8 sm:block sm:w-10"><p class="text-sm font-semibold text-red-700 dark:text-red-400">{{ $standing->losses }}</p></div>
                                    <div class="w-8 sm:w-10"><p class="text-sm font-semibold text-neutral-950 dark:text-neutral-50">{{ $standing->points }}</p></div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
