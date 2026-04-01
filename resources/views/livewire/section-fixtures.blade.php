<section data-section-fixtures-view class="ui-section">
    <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-shell-grid">
            <div>
                <div class="ui-section-intro">
                    <div class="ui-section-intro-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V8.25A2.25 2.25 0 0 1 5.25 6h13.5A2.25 2.25 0 0 1 21 8.25v10.5A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 18.75ZM3 10.5h18" />
                        </svg>
                    </div>

                    <div class="ui-section-intro-copy">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Fixtures & Results</h2>
                        <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                            {{ ($history ?? false)
                                ? 'Archived fixtures and submitted results for this section by week.'
                                : 'Current fixtures and submitted results for this section by week.' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card" data-section-fixtures-shell>
                    <div class="ui-card-column-headings justify-start px-4 sm:px-5" data-section-fixtures-headings>
                        <p class="ui-card-column-header">Home vs Away</p>
                    </div>

                    <div class="ui-card-rows" wire:loading.remove wire:target="previousWeek, nextWeek">
                        @forelse ($fixtureRows as $row)
                            <div wire:key="section-fixture-{{ $section->id }}-{{ $row->fixture->id }}">
                                @if ($row->link === null || $row->is_bye)
                                    <div>
                                @else
                                    <a class="ui-card-row-link"
                                        href="{{ $row->link }}">
                                @endif
                                        <div class="ui-card-row items-start px-4 sm:px-5" data-section-fixtures-band>
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $row->home_team_name }} <span class="font-normal text-gray-400 dark:text-gray-500">vs</span> {{ $row->away_team_name }}
                                                </p>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $row->row_meta }}
                                                </p>
                                            </div>

                                            <div class="ml-auto flex shrink-0 self-center items-center text-right">
                                                @if ($row->fixture->result)
                                                    @php
                                                        $homeScore = (int) ($row->fixture->result->home_score ?? 0);
                                                        $awayScore = (int) ($row->fixture->result->away_score ?? 0);
                                                        $homeSegmentClasses = $homeScore === $awayScore
                                                            ? 'ui-score-pill-segment-draw'
                                                            : ($homeScore > $awayScore ? 'ui-score-pill-segment-win' : 'ui-score-pill-segment-loss');
                                                        $awaySegmentClasses = $homeScore === $awayScore
                                                            ? 'ui-score-pill-segment-draw'
                                                            : ($awayScore > $homeScore ? 'ui-score-pill-segment-win' : 'ui-score-pill-segment-loss');
                                                    @endphp

                                                    <div class="ui-score-pill ui-score-pill-neutral ui-score-pill-split"
                                                        data-section-fixtures-score-pill>
                                                        <div class="ui-score-pill-segment {{ $homeSegmentClasses }} pl-1">
                                                            {{ $row->fixture->result->home_score ?? '' }}
                                                        </div>
                                                        <div class="ui-score-pill-divider-neutral"></div>
                                                        <div class="ui-score-pill-segment {{ $awaySegmentClasses }} pr-1">
                                                            {{ $row->fixture->result->away_score ?? '' }}
                                                        </div>
                                                    </div>
                                                @else
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $row->fixture->fixture_date->format('j M') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                @if ($row->link === null || $row->is_bye)
                                    </div>
                                @else
                                    </a>
                                @endif
                            </div>
                        @empty
                            <div class="ui-card-body py-10 text-center">
                                <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 px-6 py-8 dark:border-neutral-800 dark:bg-neutral-900/75">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">No fixtures available for this week.</h3>
                                    <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                                        Try another week to see upcoming fixtures or submitted results for this section.
                                    </p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="animate-pulse" wire:loading.block wire:target="previousWeek, nextWeek" data-section-fixtures-row-skeleton>
                        <div class="ui-card-rows">
                            @foreach (range(1, 5) as $row)
                                <div data-section-fixtures-row-skeleton-row>
                                    <div class="ui-card-row items-start px-4 sm:px-5" data-section-fixtures-band>
                                        <div class="min-w-0 flex-1">
                                            <div class="h-4 w-40 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                            <div class="mt-2 h-3 w-20 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                        </div>

                                        <div class="h-7 w-[60px] rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="pt-5 pb-4 lg:pt-5 lg:pb-6" data-section-fixtures-controls>
                    <div class="flex items-center justify-between gap-4" data-section-fixtures-band>
                        <button wire:click="previousWeek" wire:loading.attr="disabled"
                            class="ui-button-primary min-w-24 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0"
                            aria-label="Previous"
                            @disabled($week === 1)>
                            Previous
                        </button>

                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            Week {{ $week }}
                        </span>

                        <button wire:click="nextWeek" wire:loading.attr="disabled"
                            class="ui-button-primary min-w-24 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0"
                            aria-label="Next"
                            @disabled(! $canAdvanceWeek)>
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
