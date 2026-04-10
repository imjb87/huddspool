<section class="ui-section" data-team-fixtures-section>
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
                    Current season fixtures and results for this team.
                </p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card">
                <div class="ui-card-column-headings justify-start px-4 sm:px-5" data-team-fixtures-headings>
                    <p class="ui-card-column-header">Home vs Away</p>
                </div>

                <div class="ui-card-rows">
                    @foreach ($fixtureRows as $fixtureRow)
                        <div wire:key="team-fixture-{{ $fixtureRow->fixture_id }}">
                            @if ($fixtureRow->row_url)
                                <a href="{{ $fixtureRow->row_url }}" class="ui-card-row-link">
                            @endif
                            <div class="ui-card-row items-start px-4 sm:px-5">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 sm:hidden">
                                        {{ $fixtureRow->home_team_shortname ?: $fixtureRow->home_team_name }}
                                        <span class="font-normal text-gray-400 dark:text-gray-500">vs</span>
                                        {{ $fixtureRow->away_team_shortname ?: $fixtureRow->away_team_name }}
                                    </p>
                                    <p class="hidden text-sm font-semibold text-gray-900 dark:text-gray-100 sm:block">
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
                                        <div class="ui-score-pill ui-score-pill-split {{ $fixtureRow->result_pill_classes }}">
                                            <div class="ui-score-pill-segment pl-1">{{ $fixtureRow->home_score ?? '' }}</div>
                                            <div class="ui-score-pill-divider"></div>
                                            <div class="ui-score-pill-segment pr-1">{{ $fixtureRow->away_score ?? '' }}</div>
                                        </div>
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
            </div>

            @if ($fixtures->hasPages())
                <div class="pt-5 pb-4 lg:pt-5 lg:pb-6" data-team-fixtures-controls>
                    <div class="flex items-center justify-between gap-4">
                        @if ($fixtures->onFirstPage())
                            <span class="inline-flex w-24 items-center justify-center rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-400 dark:border-neutral-800 dark:text-gray-500">
                                Previous
                            </span>
                        @else
                            <a href="{{ $fixtures->previousPageUrl() }}"
                                class="inline-flex w-24 items-center justify-center rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-neutral-800 dark:text-gray-200 dark:hover:bg-neutral-900">
                                Previous
                            </a>
                        @endif

                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            Page {{ $fixtures->currentPage() }}
                        </span>

                        @if ($fixtures->hasMorePages())
                            <a href="{{ $fixtures->nextPageUrl() }}"
                                class="inline-flex w-24 items-center justify-center rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-neutral-800 dark:text-gray-200 dark:hover:bg-neutral-900">
                                Next
                            </a>
                        @else
                            <span class="inline-flex w-24 items-center justify-center rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-400 dark:border-neutral-800 dark:text-gray-500">
                                Next
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
