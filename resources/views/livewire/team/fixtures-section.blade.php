<section class="ui-section"
    data-team-fixtures-section
    @if ($forAccount) data-account-team-fixtures-section @endif>
    <div class="ui-shell-grid">
        <div class="ui-section-intro">
            <div class="ui-section-intro-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V8.25A2.25 2.25 0 0 1 5.25 6h13.5A2.25 2.25 0 0 1 21 8.25v10.5A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 18.75ZM3 10.5h18" />
                </svg>
            </div>
            <div class="ui-section-intro-copy">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Fixtures</h3>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    {{ $forAccount ? 'Current season fixtures and results for your team. Submission actions appear once a fixture date is due.' : 'Current season fixtures and results for this team.' }}
                </p>
            </div>
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
                        @php
                            $isReadyToSubmit = $forAccount && $fixtureRow->action_url;
                        @endphp
                        <div wire:key="team-fixture-{{ $fixtureRow->fixture_id }}">
                            @if ($fixtureRow->row_url)
                                <a href="{{ $fixtureRow->row_url }}" class="ui-card-row-link">
                            @endif
                            <div @if ($isReadyToSubmit) data-account-team-fixture-ready @endif
                                class="ui-card-row items-start px-4 sm:px-5 {{ $isReadyToSubmit ? 'bg-linear-to-r from-red-50 via-red-50/95 to-amber-50/70 text-red-900 ring-1 ring-inset ring-red-200/80 dark:from-red-950/40 dark:via-red-950/30 dark:to-amber-950/10 dark:text-red-200 dark:ring-red-900/60' : '' }}">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold {{ $isReadyToSubmit ? 'text-red-900 dark:text-red-100' : 'text-gray-900 dark:text-gray-100' }} sm:hidden">
                                        {{ $fixtureRow->home_team_shortname ?: $fixtureRow->home_team_name }}
                                        <span class="font-normal {{ $isReadyToSubmit ? 'text-red-700 dark:text-red-300' : 'text-gray-400 dark:text-gray-500' }}">vs</span>
                                        {{ $fixtureRow->away_team_shortname ?: $fixtureRow->away_team_name }}
                                    </p>
                                    <p class="hidden text-sm font-semibold {{ $isReadyToSubmit ? 'text-red-900 dark:text-red-100' : 'text-gray-900 dark:text-gray-100' }} sm:block">
                                        {{ $fixtureRow->home_team_name }}
                                        <span class="font-normal {{ $isReadyToSubmit ? 'text-red-700 dark:text-red-300' : 'text-gray-400 dark:text-gray-500' }}">vs</span>
                                        {{ $fixtureRow->away_team_name }}
                                    </p>
                                    <p class="mt-1 text-xs {{ $isReadyToSubmit ? 'text-red-800 dark:text-red-300' : 'text-gray-500 dark:text-gray-400' }}">
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
                                    @elseif ($forAccount && $fixtureRow->action_url)
                                        <p class="text-sm text-red-800 dark:text-red-300">{{ $fixtureRow->compact_date_label }}</p>
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
                                    <div class="h-4 w-40 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                    <div class="mt-2 h-3 w-20 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                </div>

                                <div class="h-7 w-[60px] rounded-full bg-gray-200 dark:bg-neutral-800"></div>
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
