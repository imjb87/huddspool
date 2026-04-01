<section class="ui-section" data-account-team-fixtures-section>
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
                    Current season fixtures and results for your team. Submission actions appear once a fixture date is due.
                </p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card" data-account-team-fixtures-shell>
                <div class="ui-card-rows">
                @forelse ($this->fixtures as $fixtureRow)
                    <div wire:key="account-team-fixture-{{ $fixtureRow->fixture_id }}">
                        @if ($fixtureRow->row_url)
                            <a class="ui-card-row-link" href="{{ $fixtureRow->row_url }}">
                        @endif
                        <div class="ui-card-row items-start px-4 sm:px-5">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 sm:hidden">
                                    {{ $fixtureRow->home_team_shortname ?: $fixtureRow->home_team_name }} <span class="font-normal text-gray-400 dark:text-gray-500">vs</span> {{ $fixtureRow->away_team_shortname ?: $fixtureRow->away_team_name }}
                                </p>
                                <p class="hidden text-sm font-semibold text-gray-900 dark:text-gray-100 sm:block">
                                    {{ $fixtureRow->home_team_name }} <span class="font-normal text-gray-400 dark:text-gray-500">vs</span> {{ $fixtureRow->away_team_name }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $fixtureRow->fixture_date_label }}</p>
                            </div>

                            <div class="ml-auto flex shrink-0 self-center items-center text-right">
                                @if ($fixtureRow->result_id)
                                    <div class="ui-score-pill ui-score-pill-split {{ $fixtureRow->result_pill_classes }}"
                                        data-section-fixtures-score-pill>
                                        <div class="ui-score-pill-segment pl-1">{{ $fixtureRow->home_score ?? '' }}</div>
                                        <div class="ui-score-pill-divider"></div>
                                        <div class="ui-score-pill-segment pr-1">{{ $fixtureRow->away_score ?? '' }}</div>
                                    </div>
                                @elseif ($fixtureRow->action_url)
                                    <span class="ui-score-pill-chip ui-score-pill-danger">
                                        {{ $fixtureRow->action_label }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if ($fixtureRow->row_url)
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="ui-card-body text-center">
                        <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 px-6 py-8 dark:border-neutral-800 dark:bg-neutral-900/75">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">No fixtures available.</h3>
                            <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                                Team fixtures will appear here once the current season schedule has been generated.
                            </p>
                        </div>
                    </div>
                @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
