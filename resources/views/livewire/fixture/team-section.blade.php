<section class="ui-section" data-{{ $sectionKey }}>
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
                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">{{ $title }}</h2>
                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                    Current player records for the {{ $side }} side in this section.
                </p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card" data-fixture-team-section-card>
                <div class="ui-averages-item-group" data-slot="item-group">
                    <div class="ui-average-item ui-average-item-header" data-slot="item" data-variant="muted" data-size="default">
                        <div class="ui-average-item-content" data-slot="item-content">
                            <div class="flex min-w-0 items-center gap-2 sm:flex-1 sm:gap-3"></div>

                            <div class="ui-average-item-stats" data-slot="item-actions">
                                <div class="ui-card-column-header w-12 sm:w-16">Played</div>
                                <div class="ui-card-column-header w-12 sm:w-16">Won</div>
                                <div class="ui-card-column-header hidden w-12 sm:block sm:w-16">Lost</div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2" wire:loading.remove wire:target="previousPage, nextPage">
                        @foreach ($this->players as $player)
                            <x-player-stats-line
                                :href="route('player.show', $player)"
                                :avatar-url="$player->avatar_url"
                                :name="$player->name"
                                :role-label="\App\Enums\UserRole::labelFor($player->role)"
                                :frames-played="$player->frames_played"
                                :frames-won="$player->frames_won"
                                :frames-lost="$player->frames_lost"
                                :show-inline-stat-labels="false"
                                wrapper-class="ui-team-player-link"
                                row-class="ui-average-item"
                                content-class="ui-average-item-content"
                                stats-class="ui-average-item-stats"
                                :hide-lost-on-mobile="true"
                                wire:key="{{ $sectionKey }}-{{ $player->id }}" />
                        @endforeach
                    </div>

                    <div class="flex flex-col gap-2 animate-pulse" wire:loading.block wire:target="previousPage, nextPage" data-{{ $sectionKey }}-loading>
                        @foreach (range(1, 5) as $row)
                            <div class="ui-average-item">
                                <div class="ui-average-item-content" data-slot="item-content">
                                    <div class="shrink-0">
                                        <div class="size-8 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="h-4 w-28 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-36"></div>
                                        <div class="mt-2 h-3 w-16 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-20"></div>
                                    </div>

                                    <div class="ui-average-item-stats" data-slot="item-actions">
                                        @foreach (range(1, 3) as $column)
                                            <div class="{{ $column === 3 ? 'hidden sm:block ' : '' }}w-12 sm:w-16">
                                                <div class="flex flex-col items-center gap-1">
                                                    <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-10"></div>
                                                    <div class="h-5 w-12 rounded-md {{ $column === 1 ? 'opacity-0' : 'bg-gray-200 dark:bg-neutral-800' }}"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if ($this->lastPage() > 1)
                    <div class="border-t border-border px-5 py-4" data-{{ $sectionKey }}-controls>
                        <nav class="ui-pagination" aria-label="{{ $title }} pagination">
                            <button wire:click="previousPage"
                                wire:loading.attr="disabled"
                                class="ui-pagination-link rounded-full"
                                aria-label="Previous page"
                                type="button"
                                @disabled($page === 1)>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left size-4" aria-hidden="true">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M15 6l-6 6l6 6" />
                                </svg>
                                <span class="hidden sm:inline">Previous</span>
                            </button>

                            <span class="ui-pagination-current" aria-live="polite">Page {{ $page }}</span>

                            <button wire:click="nextPage"
                                wire:loading.attr="disabled"
                                class="ui-pagination-link rounded-full"
                                aria-label="Next page"
                                type="button"
                                @disabled(! $this->hasNextPage())>
                                <span class="hidden sm:inline">Next</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right size-4" aria-hidden="true">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M9 6l6 6l-6 6" />
                                </svg>
                            </button>
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
