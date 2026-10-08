<section data-section-fixtures-view class="ui-section">
    <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-shell-grid">
            <div>
                <div class="ui-section-intro gap-2">
                    <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar size-5 text-neutral-700 dark:text-neutral-200">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12" />
                            <path d="M16 3v4" />
                            <path d="M8 3v4" />
                            <path d="M4 11h16" />
                            <path d="M11 15h1" />
                            <path d="M12 15v3" />
                        </svg>
                    </span>

                    <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                        <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Fixtures & Results</h2>
                        <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                            {{ ($history ?? false)
                                ? 'Review this section\'s archived fixtures and submitted results by week.'
                                : 'Follow each week\'s fixtures and open submitted results for this section.' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                @php
                    $fixtureDateLabel = $fixtureRows->first()?->row_meta;
                @endphp

                <div class="ui-card ui-fixtures-card" data-section-fixtures-shell>
                    @if ($fixtureDateLabel || ($showPrint ?? false))
                        <div class="flex items-center justify-between gap-4 border-b border-border px-5 py-4" data-section-fixtures-header>
                            @if ($fixtureDateLabel)
                                <p class="m-0 text-sm leading-5 font-medium text-neutral-700 dark:text-neutral-200" data-section-fixtures-date>{{ $fixtureDateLabel }}</p>
                            @else
                                <span aria-hidden="true"></span>
                            @endif

                            @if ($showPrint ?? false)
                                <a href="{{ route('fixture.download', ['ruleset' => $ruleset, 'section' => $section]) }}"
                                    target="_blank"
                                    class="ui-tab-trigger min-w-24 gap-2"
                                    aria-label="Print fixtures"
                                    data-section-fixtures-print>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-printer size-4" aria-hidden="true">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                        <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                        <path d="M7 15a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2l0 -4" />
                                    </svg>
                                    <span>Print</span>
                                </a>
                            @endif
                        </div>
                    @endif

                    <div wire:loading.remove wire:target="previousWeek, nextWeek" data-section-fixtures-content>
                        @if ($fixtureRows->isEmpty())
                            <x-ui-empty-state
                                title="No fixtures available for this week."
                                description="Try another week to find fixtures or results for this section."
                                data-section-fixtures-empty
                            />
                        @else
                            <div class="ui-fixtures-item-group" data-section-fixtures-list>
                                @foreach ($fixtureRows as $row)
                                    <div wire:key="section-fixture-{{ $section->id }}-{{ $row->fixture->id }}">
                                        @if ($row->link === null || $row->is_bye)
                                            <div class="ui-fixture-item" data-section-fixtures-band data-slot="item" data-variant="muted" data-size="default">
                                        @else
                                            <a class="ui-fixture-item" data-section-fixtures-band data-slot="item" data-variant="muted" data-size="default"
                                                href="{{ $row->link }}">
                                        @endif
                                                <div class="ui-fixture-item-content" data-slot="item-content">
                                                    <div class="ui-fixture-team-matchup">
                                                        <div class="ui-fixture-team-names">
                                                            <p class="ui-fixture-team-name">{{ $row->home_team_name }}</p>
                                                            <p class="ui-fixture-team-name">{{ $row->away_team_name }}</p>
                                                        </div>

                                                        @if ($row->fixture->result)
                                                            @php
                                                                $homeScore = (int) ($row->fixture->result->home_score ?? 0);
                                                                $awayScore = (int) ($row->fixture->result->away_score ?? 0);
                                                                $homeBadgeClasses = $homeScore === $awayScore
                                                                    ? 'ui-live-score-badge-draw'
                                                                    : ($homeScore > $awayScore ? 'ui-live-score-badge-win' : 'ui-live-score-badge-loss');
                                                                $awayBadgeClasses = $homeScore === $awayScore
                                                                    ? 'ui-live-score-badge-draw'
                                                                    : ($awayScore > $homeScore ? 'ui-live-score-badge-win' : 'ui-live-score-badge-loss');
                                                            @endphp

                                                            <div class="ui-fixture-item-actions" data-slot="item-actions">
                                                                <div class="ui-fixture-badge-stack" role="group" aria-label="{{ $row->home_team_name }} {{ $row->fixture->result->home_score }} to {{ $row->fixture->result->away_score }} {{ $row->away_team_name }}" data-section-fixtures-score-stack>
                                                                    <span class="ui-fixture-badge {{ $homeBadgeClasses }}" data-slot="badge">{{ $row->fixture->result->home_score }}</span>
                                                                    <span class="ui-fixture-badge {{ $awayBadgeClasses }}" data-slot="badge">{{ $row->fixture->result->away_score }}</span>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="ui-fixture-item-actions self-center">
                                                                <span class="ui-fixture-badge ui-fixture-badge-neutral" data-slot="badge" aria-label="Fixture date {{ $row->fixture->fixture_date->format('j M') }}">{{ $row->fixture->fixture_date->format('j M') }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                        @if ($row->link === null || $row->is_bye)
                                            </div>
                                        @else
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="animate-pulse" wire:loading.block wire:target="previousWeek, nextWeek" data-section-fixtures-row-skeleton>
                        <div class="ui-fixtures-date-heading">
                            <div class="h-4 w-24 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                        </div>

                        <div class="ui-fixtures-item-group">
                            @foreach (range(1, 5) as $row)
                                <div class="ui-fixture-item" data-section-fixtures-row-skeleton-row data-section-fixtures-band>
                                    <div class="ui-fixture-item-content">
                                        <div class="ui-fixture-team-matchup">
                                            <div class="ui-fixture-team-names">
                                                <div class="h-4 w-40 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                                <div class="h-4 w-36 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                            </div>

                                            <div class="ui-fixture-badge-stack">
                                                <div class="h-5 min-w-7 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                                <div class="h-5 min-w-7 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t border-border px-5 py-4" data-section-fixtures-controls>
                        <nav class="ui-fixtures-pagination" aria-label="Fixtures pagination" data-section-fixtures-pagination data-section-fixtures-band>
                            <button wire:click="previousWeek" wire:loading.attr="disabled"
                                class="ui-pagination-link rounded-full"
                                aria-label="Go to previous week"
                                type="button"
                                @disabled($week === 1)>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left size-4" aria-hidden="true">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M15 6l-6 6l6 6" />
                                </svg>
                                <span class="hidden sm:inline">Previous</span>
                            </button>

                            <span class="ui-pagination-current" aria-live="polite">
                                Week {{ $week }}
                            </span>

                            <button wire:click="nextWeek" wire:loading.attr="disabled"
                                class="ui-pagination-link rounded-full"
                                aria-label="Go to next week"
                                type="button"
                                @disabled(! $canAdvanceWeek)>
                                <span class="hidden sm:inline">Next</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right size-4" aria-hidden="true">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M9 6l6 6l-6 6" />
                                </svg>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
