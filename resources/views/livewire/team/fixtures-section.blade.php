<section class="ui-section"
    data-team-fixtures-section
    @if ($forAccount) data-account-team-fixtures-section @endif>
    <div class="ui-shell-grid">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Fixtures</h3>
            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                {{ $forAccount ? 'Current season fixtures and results for your team. Submission actions appear once a fixture date is due.' : 'Current season fixtures and results for this team.' }}
            </p>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card" @if ($forAccount) data-account-team-fixtures-shell @endif>
                <div class="ui-card-column-headings justify-start px-4 sm:px-5" data-team-fixtures-headings>
                    <p class="ui-card-column-header">Home vs Away</p>
                </div>

                <div class="ui-card-rows"
                    wire:loading.remove
                    wire:target="previousPage, nextPage">
                    @foreach ($this->fixtureRows as $fixtureRow)
                        <div wire:key="team-fixture-{{ $fixtureRow->fixture_id }}">
                            @if ($fixtureRow->row_url)
                                <a href="{{ $fixtureRow->row_url }}" class="ui-card-row-link">
                            @endif
                            <div class="ui-card-row items-start px-4 sm:px-5">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $fixtureRow->home_team_name }}
                                        <span class="font-normal text-gray-400 dark:text-gray-500">vs</span>
                                        {{ $fixtureRow->away_team_name }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $fixtureRow->fixture_date_label }}
                                    </p>
                                </div>

                                <div class="ml-auto flex shrink-0 self-center items-center text-right">
                                    @if ($fixtureRow->result_id)
                                        <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full {{ $fixtureRow->result_pill_classes }} text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10">
                                            <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">{{ $fixtureRow->home_score ?? '' }}</div>
                                            <div class="w-px bg-white/25"></div>
                                            <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">{{ $fixtureRow->away_score ?? '' }}</div>
                                        </div>
                                    @elseif ($fixtureRow->action_url)
                                        <span class="inline-flex h-7 min-w-[60px] items-center justify-center rounded-full bg-linear-to-br from-red-700 via-red-600 to-red-500 px-3 text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10">
                                            {{ $fixtureRow->action_label }}
                                        </span>
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $fixtureRow->compact_date_label }}</p>
                                    @endif
                                </div>
                            </div>
                            @if ($fixtureRow->row_url)
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="animate-pulse"
                    wire:loading.block
                    wire:target="previousPage, nextPage"
                    data-team-fixtures-loading>
                    <div class="ui-card-rows">
                        @foreach (range(1, 5) as $row)
                            <div class="ui-card-row items-start px-4 sm:px-5">
                                <div class="min-w-0 flex-1">
                                    <div class="h-4 w-40 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                    <div class="mt-2 h-3 w-20 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                </div>

                                <div class="h-7 w-[60px] rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if ($this->lastPage() > 1)
                <div class="pt-5 pb-4 lg:pt-5 lg:pb-6" @if ($forAccount) data-account-team-fixtures-controls @else data-team-fixtures-controls @endif>
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
