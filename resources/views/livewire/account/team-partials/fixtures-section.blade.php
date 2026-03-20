<section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-account-team-fixtures-section>
    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Fixtures</h3>
            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Current season fixtures and results for your team. Submission actions appear once a fixture date is due.
            </p>
        </div>

        <div class="lg:col-span-2">
            <div class="divide-y divide-gray-200 dark:divide-zinc-800/80" data-account-team-fixtures-shell>
                @forelse ($this->fixtures as $item)
                    @php
                        $fixture = $item->fixture;
                        $rowUrl = $fixture->result ? route('result.show', $fixture->result) : (! $item->action_url ? route('fixture.show', $fixture) : null);
                        $isDraw = $fixture->result
                            && (int) $fixture->result->home_score === (int) $fixture->result->away_score;
                        $teamWon = $fixture->result
                            && (($fixture->home_team_id == $this->team->id && (int) $fixture->result->home_score > (int) $fixture->result->away_score)
                            || ($fixture->away_team_id == $this->team->id && (int) $fixture->result->away_score > (int) $fixture->result->home_score));
                        $resultPillClasses = $isDraw
                            ? 'bg-linear-to-br from-gray-600 via-gray-500 to-gray-400'
                            : ($teamWon
                                ? 'bg-linear-to-br from-green-900 via-green-800 to-green-700'
                                : 'bg-linear-to-br from-red-800 via-red-700 to-red-600');
                    @endphp
                    <div class="py-4" wire:key="account-team-fixture-{{ $fixture->id }}">
                        @if ($rowUrl)
                            <a class="block rounded-lg hover:cursor-pointer" href="{{ $rowUrl }}">
                        @endif
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $fixture->homeTeam?->name }} <span class="font-normal text-gray-400 dark:text-gray-500">vs</span> {{ $fixture->awayTeam?->name }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $fixture->fixture_date->format('j M Y') }}</p>
                            </div>

                            <div class="ml-auto flex shrink-0 self-center items-center text-right">
                                @if ($fixture->result)
                                    <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full {{ $resultPillClasses }} text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10"
                                        data-section-fixtures-score-pill>
                                        <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">{{ $fixture->result->home_score ?? '' }}</div>
                                        <div class="w-px bg-white/25"></div>
                                        <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">{{ $fixture->result->away_score ?? '' }}</div>
                                    </div>
                                @elseif ($item->action_url)
                                    <a href="{{ $item->action_url }}"
                                        class="inline-flex h-7 min-w-[60px] items-center justify-center rounded-full bg-linear-to-br from-red-700 via-red-600 to-red-500 px-3 text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10 transition hover:brightness-110">
                                        {{ $item->action_label }}
                                    </a>
                                @endif
                            </div>
                        </div>
                        @if ($rowUrl)
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="px-4 py-10 text-center sm:px-6">
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
</section>
