<section class="ui-section" data-{{ $sectionKey }}>
    <div class="ui-shell-grid">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h3>
            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Current player records for the {{ $side }} side in this section.
            </p>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card">
                <div class="ui-card-column-headings px-4 sm:px-5">
                    <div class="flex min-w-0 items-center gap-3 sm:gap-4"></div>

                    <div class="ml-auto flex shrink-0 items-start gap-2 text-center sm:gap-5">
                        <div class="w-12 sm:w-16">
                            <p class="ui-card-column-header">Played</p>
                        </div>
                        <div class="w-12 sm:w-16">
                            <p class="ui-card-column-header">Won</p>
                        </div>
                        <div class="w-12 sm:w-16">
                            <p class="ui-card-column-header">Lost</p>
                        </div>
                    </div>
                </div>

                <div class="ui-card-rows"
                    wire:loading.remove
                    wire:target="previousPage, nextPage">
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
                            wrapper-class="ui-card-row-link"
                            row-class="ui-card-row items-center px-4 sm:px-5"
                            wire:key="{{ $sectionKey }}-{{ $player->id }}" />
                    @endforeach
                </div>

                <div class="animate-pulse"
                    wire:loading.block
                    wire:target="previousPage, nextPage"
                    data-{{ $sectionKey }}-loading>
                    <div class="ui-card-rows">
                        @foreach (range(1, 5) as $row)
                            <div class="ui-card-row items-center px-4 sm:px-5">
                                <div class="shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="h-4 w-28 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-36"></div>
                                    <div class="mt-2 h-3 w-16 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-20"></div>
                                </div>

                                <div class="ml-auto flex shrink-0 items-center gap-2 sm:gap-5">
                                    @foreach (range(1, 3) as $column)
                                        <div class="w-12 sm:w-16">
                                            <div class="flex flex-col items-center gap-1">
                                                <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-10"></div>
                                                <div class="h-5 w-12 rounded-md {{ $column === 1 ? 'opacity-0' : 'bg-gray-200 dark:bg-neutral-800' }}"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if ($this->lastPage() > 1)
                <div class="pt-5 pb-4 lg:pt-5 lg:pb-6" data-{{ $sectionKey }}-controls>
                    <div class="flex items-center justify-between gap-4">
                        <button wire:click="previousPage"
                            wire:loading.attr="disabled"
                            class="ui-button-primary min-w-24 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0"
                            aria-label="Previous"
                            @disabled($page === 1)>
                            Previous
                        </button>

                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            Page {{ $page }}
                        </span>

                        <button wire:click="nextPage"
                            wire:loading.attr="disabled"
                            class="ui-button-primary min-w-24 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0"
                            aria-label="Next"
                            @disabled(! $this->hasNextPage())>
                            Next
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
