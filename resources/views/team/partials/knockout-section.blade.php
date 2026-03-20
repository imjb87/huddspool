@if ($teamKnockoutMatches->isNotEmpty())
    <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-team-knockout-section>
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Team knockouts</h3>
                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Recent team knockout ties and completed results.
                </p>
            </div>

            <div class="lg:col-span-2">
                <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                    @foreach ($teamKnockoutMatches as $match)
                        @php
                            $hasResult = $match->home_score !== null && $match->away_score !== null;
                            $rowUrl = $hasResult ? route('knockout.show', $match->round->knockout) : null;
                            $teamParticipantId = null;

                            if ($match->homeParticipant?->team_id === $team->id) {
                                $teamParticipantId = $match->homeParticipant?->id;
                            } elseif ($match->awayParticipant?->team_id === $team->id) {
                                $teamParticipantId = $match->awayParticipant?->id;
                            }

                            $wonMatch = $hasResult && $match->winner_participant_id && $teamParticipantId === $match->winner_participant_id;
                            $isDraw = $hasResult && (int) $match->home_score === (int) $match->away_score;
                            $resultPillClasses = $isDraw
                                ? 'bg-linear-to-br from-gray-600 via-gray-500 to-gray-400'
                                : ($wonMatch
                                    ? 'bg-linear-to-br from-green-900 via-green-800 to-green-700'
                                    : 'bg-linear-to-br from-red-800 via-red-700 to-red-600');
                        @endphp
                        <div wire:key="team-knockout-{{ $match->id }}">
                            @if ($rowUrl)
                                <a href="{{ $rowUrl }}" class="block rounded-lg transition">
                            @endif
                            <div class="flex items-start gap-3 py-4 sm:items-center sm:gap-4">
                                <div class="min-w-0 flex-1">
                                    <p class="[overflow-wrap:anywhere] text-sm leading-5 font-semibold text-gray-900 dark:text-gray-100">
                                        <span>{{ $match->homeParticipant?->display_name ?? 'TBC' }}</span>
                                        <span class="px-1 font-normal text-gray-400 dark:text-gray-500">vs</span>
                                        <span>{{ $match->awayParticipant?->display_name ?? 'TBC' }}</span>
                                    </p>
                                    <p class="mt-1 [overflow-wrap:anywhere] text-xs leading-5 text-gray-500 dark:text-gray-400">
                                        {{ $match->round?->knockout?->name ?? 'Knockout' }}
                                        <span class="text-gray-300 dark:text-zinc-600">/</span>
                                        {{ $match->round?->name ?? 'Round TBC' }}
                                    </p>
                                </div>

                                <div class="shrink-0 text-right">
                                    @if ($hasResult)
                                        <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full {{ $resultPillClasses }} text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10">
                                            <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">{{ $match->home_score }}</div>
                                            <div class="w-px bg-white/25"></div>
                                            <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">{{ $match->away_score }}</div>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $match->starts_at ? $match->starts_at->format('j M') : 'Date TBC' }}
                                        </p>
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
@endif
