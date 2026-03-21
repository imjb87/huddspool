<section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-team-fixtures-section>
    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Fixtures</h3>
            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Current season fixtures and results for this team.
            </p>
        </div>

        <div class="lg:col-span-2">
            <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                @foreach ($fixtureRows as $fixtureRow)
                    <div wire:key="team-fixture-{{ $fixtureRow->fixture_id }}">
                        @if ($fixtureRow->row_url)
                            <a href="{{ $fixtureRow->row_url }}" class="block py-4 transition sm:rounded-lg sm:px-3 sm:hover:bg-gray-200/70 dark:sm:hover:bg-zinc-800/70">
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
</section>
