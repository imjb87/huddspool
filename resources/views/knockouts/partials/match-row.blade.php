<article class="border-t border-gray-300 first:border-t-0 dark:border-zinc-800/80" data-knockout-match-row>
    @php
        $shouldFadeVenue = $match->venue && \Illuminate\Support\Str::length($match->venue->name) > 18;
    @endphp

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-3 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:gap-6">
        <div class="flex items-start justify-between gap-3 lg:hidden">
            @if ($matchLabel)
                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500" data-knockout-match-label>
                    {{ $matchLabel }}
                </p>
            @endif

            <div class="text-right text-[11px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 lg:hidden">
                @if (! $hasBye)
                    <div>
                        @if ($match->venue)
                            <a href="{{ route('venue.show', $match->venue) }}" class="whitespace-nowrap transition hover:text-gray-600 dark:hover:text-gray-300">
                                {{ $match->venue->name }}
                            </a>
                        @else
                            Venue TBC
                        @endif
                    </div>
                @endif
                @if ($match->referee)
                    <div>Referee: {{ $match->referee }}</div>
                @endif
            </div>
        </div>

        <div class="flex-1 lg:min-w-0">
            <div class="grid grid-cols-[minmax(0,1fr)_88px_minmax(0,1fr)] items-center gap-3 lg:grid-cols-[128px_minmax(0,1fr)_88px_minmax(0,1fr)_128px]">
                <div class="hidden lg:block text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">
                    @if ($matchLabel)
                        <p data-knockout-match-label>{{ $matchLabel }}</p>
                    @endif
                </div>

                <div class="min-w-0 px-0 py-0 text-right">
                    @php($homeParticipant = $match->homeParticipant)
                    <div class="text-sm leading-6 font-semibold text-gray-900 dark:text-gray-100">
                        @if ($homeParticipant)
                            @if ($knockout->type === \App\KnockoutType::Doubles)
                                @php($homePlayers = [$homeParticipant->playerOne, $homeParticipant->playerTwo])
                                <div class="flex flex-col items-end gap-0.5">
                                    @foreach ($homePlayers as $player)
                                        @if ($player)
                                            <a href="{{ route('player.show', $player) }}" class="truncate whitespace-nowrap leading-6 transition hover:text-gray-500 dark:hover:text-gray-300">
                                                {{ $player->name }}
                                            </a>
                                        @else
                                            <span class="leading-6 text-gray-500 dark:text-gray-400">TBC</span>
                                        @endif
                                    @endforeach
                                </div>
                            @elseif ($homeParticipant->playerOne)
                                <a href="{{ route('player.show', $homeParticipant->playerOne) }}" class="block truncate whitespace-nowrap leading-6 transition hover:text-gray-500 dark:hover:text-gray-300">
                                    {{ $homeLabel }}
                                </a>
                            @elseif ($homeParticipant->team)
                                <a href="{{ route('team.show', $homeParticipant->team) }}" class="block truncate whitespace-nowrap leading-6 transition hover:text-gray-500 dark:hover:text-gray-300">
                                    {{ $homeLabel }}
                                </a>
                            @else
                                {{ $homeLabel }}
                            @endif
                        @else
                            {{ $homeLabel }}
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-center" data-knockout-score-state>
                    @if ($match->forfeitParticipant)
                        <span class="inline-flex h-7 min-w-[60px] items-center justify-center rounded-full bg-gray-100 px-3 text-xs font-bold uppercase tracking-wide text-gray-600 ring-1 ring-gray-200 dark:bg-zinc-700 dark:text-gray-300 dark:ring-zinc-700">
                            FF
                        </span>
                    @elseif ($match->home_score !== null && $match->away_score !== null)
                        <span class="inline-flex h-7 w-[60px] overflow-hidden rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10"
                            data-knockout-score-pill>
                            <span class="flex w-1/2 items-center justify-center tabular-nums pl-1">
                                {{ $match->home_score }}
                            </span>
                            <span class="w-px bg-white/25"></span>
                            <span class="flex w-1/2 items-center justify-center tabular-nums pr-1">
                                {{ $match->away_score }}
                            </span>
                        </span>
                    @elseif ($match->starts_at)
                        <span class="whitespace-nowrap text-center text-sm font-semibold text-gray-500 dark:text-gray-400">
                            {{ $match->starts_at->format('d/m') }}
                        </span>
                    @else
                        <span class="inline-flex h-7 min-w-[60px] items-center justify-center rounded-full bg-gray-100 px-3 text-xs font-semibold uppercase tracking-wide text-gray-400 ring-1 ring-gray-200 dark:bg-zinc-700 dark:text-gray-500 dark:ring-zinc-700">
                            Vs
                        </span>
                    @endif
                </div>

                <div class="min-w-0 px-0 py-0 text-left">
                    @php($awayParticipant = $match->awayParticipant)
                    <div class="text-sm leading-6 font-semibold text-gray-900 dark:text-gray-100">
                        @if ($awayParticipant)
                            @if ($knockout->type === \App\KnockoutType::Doubles)
                                @php($awayPlayers = [$awayParticipant->playerOne, $awayParticipant->playerTwo])
                                <div class="flex flex-col gap-0.5">
                                    @foreach ($awayPlayers as $player)
                                        @if ($player)
                                            <a href="{{ route('player.show', $player) }}" class="truncate whitespace-nowrap leading-6 transition hover:text-gray-500 dark:hover:text-gray-300">
                                                {{ $player->name }}
                                            </a>
                                        @else
                                            <span class="leading-6 text-gray-500 dark:text-gray-400">TBC</span>
                                        @endif
                                    @endforeach
                                </div>
                            @elseif ($awayParticipant->playerOne)
                                <a href="{{ route('player.show', $awayParticipant->playerOne) }}" class="block truncate whitespace-nowrap leading-6 transition hover:text-gray-500 dark:hover:text-gray-300">
                                    {{ $awayLabel }}
                                </a>
                            @elseif ($awayParticipant->team)
                                <a href="{{ route('team.show', $awayParticipant->team) }}" class="block truncate whitespace-nowrap leading-6 transition hover:text-gray-500 dark:hover:text-gray-300">
                                    {{ $awayLabel }}
                                </a>
                            @else
                                {{ $awayLabel }}
                            @endif
                        @else
                            {{ $awayLabel }}
                        @endif
                    </div>
                </div>

                <div class="hidden lg:block min-w-0 text-right text-[11px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">
                    @if (! $hasBye)
                        <div class="min-w-0">
                            @if ($match->venue)
                                <a href="{{ route('venue.show', $match->venue) }}"
                                    @class([
                                        'block overflow-hidden whitespace-nowrap transition hover:text-gray-600 dark:hover:text-gray-300',
                                        'text-clip' => ! $shouldFadeVenue,
                                    ])
                                    @if ($shouldFadeVenue)
                                        style="-webkit-mask-image: linear-gradient(to right, black calc(100% - 1.5rem), transparent); mask-image: linear-gradient(to right, black calc(100% - 1.5rem), transparent);"
                                    @endif
                                    title="{{ $match->venue->name }}">
                                    {{ $match->venue->name }}
                                </a>
                            @else
                                Venue TBC
                            @endif
                        </div>
                    @endif
                    @if ($match->referee)
                        <div>Referee: {{ $match->referee }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</article>
