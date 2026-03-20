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
                @foreach ($fixtures as $fixture)
                    @php
                        $rowUrl = $fixture->isBye()
                            ? null
                            : ($fixture->result_id ? route('result.show', $fixture->result_id) : route('fixture.show', $fixture->id));
                        $isDraw = $fixture->result_id
                            && (int) $fixture->home_score === (int) $fixture->away_score;
                        $teamWon = $fixture->result_id
                            && (($fixture->home_team_id == $team->id && (int) $fixture->home_score > (int) $fixture->away_score)
                            || ($fixture->away_team_id == $team->id && (int) $fixture->away_score > (int) $fixture->home_score));
                        $resultPillClasses = $isDraw
                            ? 'bg-linear-to-br from-gray-600 via-gray-500 to-gray-400'
                            : ($teamWon
                                ? 'bg-linear-to-br from-green-900 via-green-800 to-green-700'
                                : 'bg-linear-to-br from-red-800 via-red-700 to-red-600');
                    @endphp
                    <div wire:key="team-fixture-{{ $fixture->id }}">
                        @if ($rowUrl)
                            <a href="{{ $rowUrl }}" class="block rounded-lg transition">
                        @endif
                        <div class="flex items-start justify-between gap-4 py-4">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $fixture->home_team_name }}
                                    <span class="font-normal text-gray-400 dark:text-gray-500">vs</span>
                                    {{ $fixture->away_team_name }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ optional($fixture->fixture_date)->format('j M Y') ?? 'Date TBC' }}
                                </p>
                            </div>

                            <div class="ml-auto flex shrink-0 self-center items-center text-right">
                                @if ($fixture->result_id)
                                    <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full {{ $resultPillClasses }} text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10">
                                        <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">{{ $fixture->home_score ?? '' }}</div>
                                        <div class="w-px bg-white/25"></div>
                                        <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">{{ $fixture->away_score ?? '' }}</div>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ optional($fixture->fixture_date)->format('j M') ?? 'TBC' }}</p>
                                @endif
                            </div>
                        </div>
                        @if ($rowUrl)
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
