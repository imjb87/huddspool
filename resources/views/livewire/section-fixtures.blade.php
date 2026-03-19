<section data-section-fixtures-view class="mt-0">
    @php
        $isHistoryView = $history ?? false;
        $summaryCopy = $isHistoryView
            ? 'Archived fixtures and submitted results for this section by week.'
            : 'Current fixtures and submitted results for this section by week.';
    @endphp
    <div class="mx-auto mt-6 w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Fixtures & results</h2>
                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    {{ $summaryCopy }}
                </p>
            </div>

            <div class="lg:col-span-2">
                <div data-section-fixtures-shell>
                    <div class="divide-y divide-gray-200 dark:divide-zinc-800/80" wire:loading.remove wire:target="previousWeek, nextWeek">
                        @forelse ($fixtures as $fixture)
                            @php
                                $isByeFixture = $fixture->home_team_id == 1 || $fixture->away_team_id == 1;
                                $homeDisplayName = $isHistoryView && $fixture->result?->home_team_name
                                    ? $fixture->result->home_team_name
                                    : $fixture->homeTeam->name;
                                $awayDisplayName = $isHistoryView && $fixture->result?->away_team_name
                                    ? $fixture->result->away_team_name
                                    : $fixture->awayTeam->name;
                                $homeTeamName = $fixture->homeTeam->shortname && ! $isHistoryView ? $fixture->homeTeam->shortname : $homeDisplayName;
                                $awayTeamName = $fixture->awayTeam->shortname && ! $isHistoryView ? $fixture->awayTeam->shortname : $awayDisplayName;
                                $rowMeta = $fixture->fixture_date->format('j M Y');
                                $rowClasses = 'block rounded-lg';
                            @endphp

                            <div wire:key="section-fixture-{{ $section->id }}-{{ $fixture->id }}">
                                @if ($isHistoryView || $isByeFixture)
                                    <div class="{{ $rowClasses }}">
                                @else
                                    <a class="{{ $rowClasses }}"
                                        href="{{ $fixture->result ? route('result.show', $fixture->result) : route('fixture.show', $fixture) }}">
                                @endif
                                        <div class="flex items-start justify-between gap-4 py-4" data-section-fixtures-band>
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $homeTeamName }} <span class="font-normal text-gray-400 dark:text-gray-500">vs</span> {{ $awayTeamName }}
                                                </p>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $rowMeta }}
                                                </p>
                                            </div>

                                            <div class="ml-auto flex shrink-0 self-center items-center text-right">
                                                @if ($fixture->result)
                                                    <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10"
                                                        data-section-fixtures-score-pill>
                                                        <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">
                                                            {{ $fixture->result->home_score ?? '' }}
                                                        </div>
                                                        <div class="w-px bg-white/25"></div>
                                                        <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">
                                                            {{ $fixture->result->away_score ?? '' }}
                                                        </div>
                                                    </div>
                                                @else
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $fixture->fixture_date->format('j M') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                @if ($isHistoryView || $isByeFixture)
                                    </div>
                                @else
                                    </a>
                                @endif
                            </div>
                        @empty
                            <div class="px-4 py-10 text-center sm:px-6">
                                <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 px-6 py-8 dark:border-zinc-700 dark:bg-zinc-800/75">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">No fixtures available for this week.</h3>
                                    <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                                        Try another week to see upcoming fixtures or submitted results for this section.
                                    </p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="animate-pulse" wire:loading.block wire:target="previousWeek, nextWeek" data-section-fixtures-row-skeleton>
                        <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                            @foreach (range(1, 5) as $row)
                                <div class="py-4" data-section-fixtures-row-skeleton-row>
                                    <div class="flex items-center justify-between gap-4" data-section-fixtures-band>
                                        <div class="min-w-0 flex-1">
                                            <div class="h-4 w-32 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                            <div class="mt-2 h-3 w-20 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                        </div>

                                        <div class="h-7 w-[60px] rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="pt-5 pb-4 lg:pt-5 lg:pb-6" data-section-fixtures-controls>
                    <div class="flex items-center justify-between gap-4" data-section-fixtures-band>
                        <button wire:click="previousWeek" wire:loading.attr="disabled"
                            class="inline-flex w-24 cursor-pointer items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-3 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                            aria-label="Previous"
                            {{ $week == 1 ? 'disabled' : '' }}>
                            Previous
                        </button>

                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            Week {{ $week }}
                        </span>

                        <button wire:click="nextWeek" wire:loading.attr="disabled"
                            class="inline-flex w-24 cursor-pointer items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-3 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                            aria-label="Next"
                            {{ $week >= 18 ? 'disabled' : '' }}>
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
