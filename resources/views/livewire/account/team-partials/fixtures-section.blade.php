<section class="ui-section" data-account-team-fixtures-section>
    <div class="ui-shell-grid">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Fixtures</h3>
            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Current season fixtures and results for your team. Submission actions appear once a fixture date is due.
            </p>
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
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $fixtureRow->home_team_name }} <span class="font-normal text-gray-400 dark:text-gray-500">vs</span> {{ $fixtureRow->away_team_name }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $fixtureRow->fixture_date_label }}</p>
                            </div>

                            <div class="ml-auto flex shrink-0 self-center items-center text-right">
                                @if ($fixtureRow->result_id)
                                    <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full {{ $fixtureRow->result_pill_classes }} text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10"
                                        data-section-fixtures-score-pill>
                                        <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">{{ $fixtureRow->home_score ?? '' }}</div>
                                        <div class="w-px bg-white/25"></div>
                                        <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">{{ $fixtureRow->away_score ?? '' }}</div>
                                    </div>
                                @elseif ($fixtureRow->action_url)
                                    <span
                                        class="inline-flex h-7 min-w-[60px] items-center justify-center rounded-full bg-linear-to-br from-red-700 via-red-600 to-red-500 px-3 text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10">
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
                        <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 px-6 py-8 dark:border-zinc-700 dark:bg-zinc-800/75">
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
