<section class="ui-section"
    data-team-fixtures-section
    @if ($forAccount) data-account-team-fixtures-section @endif>
    <div class="ui-shell-grid">
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
                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Fixtures</h2>
                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                    {{ $forAccount ? 'Current season fixtures and results for your team. Submission actions appear once a fixture date is due.' : 'Current season fixtures and results for this team.' }}
                </p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card ui-fixtures-card" @if ($forAccount) data-account-team-fixtures-shell @endif>
                <div class="ui-fixtures-item-group"
                    wire:loading.remove
                    wire:target="previousPage, nextPage"
                    data-team-fixtures-headings>
                    @foreach ($this->fixtureRows as $fixtureRow)
                        @php
                            $isReadyToSubmit = ($forAccount || $showSubmissionActions) && $fixtureRow->action_url;
                            $hasScore = $fixtureRow->result_id !== null
                                && $fixtureRow->home_score !== null
                                && $fixtureRow->away_score !== null;
                            $isDraw = $hasScore && (int) $fixtureRow->home_score === (int) $fixtureRow->away_score;
                            $homeBadgeClasses = $isDraw
                                ? 'ui-live-score-badge-draw'
                                : ((int) $fixtureRow->home_score > (int) $fixtureRow->away_score
                                    ? 'ui-live-score-badge-win'
                                    : 'ui-live-score-badge-loss');
                            $awayBadgeClasses = $isDraw
                                ? 'ui-live-score-badge-draw'
                                : ((int) $fixtureRow->away_score > (int) $fixtureRow->home_score
                                    ? 'ui-live-score-badge-win'
                                    : 'ui-live-score-badge-loss');
                            $itemClasses = $isReadyToSubmit
                                ? 'ui-team-fixture-ready'
                                : '';
                        @endphp

                        <div wire:key="team-fixture-{{ $fixtureRow->fixture_id }}">
                            @if ($fixtureRow->row_url)
                                <a href="{{ $fixtureRow->row_url }}"
                                    class="ui-fixture-item {{ $itemClasses }}"
                                    @if ($isReadyToSubmit) data-account-team-fixture-ready @endif
                                    data-slot="item"
                                    data-variant="muted"
                                    data-size="default">
                            @else
                                <div class="ui-fixture-item {{ $itemClasses }}"
                                    @if ($isReadyToSubmit) data-account-team-fixture-ready @endif
                                    data-slot="item"
                                    data-variant="muted"
                                    data-size="default">
                            @endif
                                    <div class="ui-fixture-item-content" data-slot="item-content">
                                        <div class="ui-fixture-team-matchup">
                                            <div class="ui-fixture-team-names">
                                                <p class="ui-fixture-team-name">
                                                    <span class="sm:hidden">{{ $fixtureRow->home_team_shortname ?: $fixtureRow->home_team_name }}</span>
                                                    <span class="hidden sm:inline">{{ $fixtureRow->home_team_name }}</span>
                                                </p>
                                                <p class="ui-fixture-team-name">
                                                    <span class="sm:hidden">{{ $fixtureRow->away_team_shortname ?: $fixtureRow->away_team_name }}</span>
                                                    <span class="hidden sm:inline">{{ $fixtureRow->away_team_name }}</span>
                                                </p>
                                            </div>

                                            <div class="ui-fixture-item-actions self-center" data-slot="item-actions">
                                                @if ($hasScore)
                                                    <div class="ui-fixture-badge-stack" role="group" aria-label="{{ $fixtureRow->home_team_name }} {{ $fixtureRow->home_score }} to {{ $fixtureRow->away_score }} {{ $fixtureRow->away_team_name }}">
                                                        <span class="ui-fixture-badge {{ $homeBadgeClasses }}" data-slot="badge">{{ $fixtureRow->home_score }}</span>
                                                        <span class="ui-fixture-badge {{ $awayBadgeClasses }}" data-slot="badge">{{ $fixtureRow->away_score }}</span>
                                                    </div>
                                                @else
                                                    <span class="ui-fixture-badge ui-fixture-badge-neutral" data-slot="badge" aria-label="Fixture date {{ $fixtureRow->compact_date_label }}">{{ $fixtureRow->compact_date_label }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                            @if ($fixtureRow->row_url)
                                </a>
                            @else
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="animate-pulse" wire:loading.block wire:target="previousPage, nextPage" data-team-fixtures-loading>
                    <div class="ui-fixtures-item-group">
                        @foreach (range(1, 5) as $row)
                            <div class="ui-fixture-item">
                                <div class="ui-fixture-item-content">
                                    <div class="ui-fixture-team-matchup">
                                        <div class="ui-fixture-team-names">
                                            <div class="h-3 w-20 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
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

                @if ($this->lastPage() > 1)
                    <div class="border-t border-border px-5 py-4" @if ($forAccount) data-account-team-fixtures-controls @else data-team-fixtures-controls @endif>
                        <nav class="ui-pagination" aria-label="Team fixtures pagination">
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
