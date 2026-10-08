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
                    Follow your fixtures and submit results once match nights are complete.
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
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $fixtureRow->compact_date_label }}</p>
                                @endif
                            </div>
                        </div>
                        @if ($fixtureRow->row_url)
                            </a>
                        @endif
                    </div>
                @empty
                    <x-ui-empty-state
                        title="No fixtures available."
                        description="Your fixtures will appear here when the current season schedule is published."
                        data-account-team-fixtures-empty
                    />
                @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
