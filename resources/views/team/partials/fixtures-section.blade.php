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
                <div class="divide-y divide-gray-200 dark:divide-neutral-800/75">
                    @foreach ($fixtureRows as $fixtureRow)
                        <div wire:key="team-fixture-{{ $fixtureRow->fixture_id }}">
                            @if ($fixtureRow->row_url)
                                <a href="{{ $fixtureRow->row_url }}" class="block py-4 transition sm:-mx-3 sm:-my-px sm:rounded-lg sm:px-3 sm:hover:bg-gray-200/70 dark:sm:hover:bg-neutral-900/70">
                            @endif
                            <div class="flex items-start justify-between gap-4 {{ $fixtureRow->row_url ? '' : 'py-4 sm:rounded-lg sm:px-3' }}">
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
        </div>
    </div>
</section>
