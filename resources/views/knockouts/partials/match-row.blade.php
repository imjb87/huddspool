<article class="py-4" data-knockout-match-row>
    @php
        $homeParticipant = $match->homeParticipant;
        $awayParticipant = $match->awayParticipant;
    @endphp

    <div class="flex items-start justify-between gap-4" data-section-fixtures-band>
        <div class="min-w-0 flex-1">
            <p class="[overflow-wrap:anywhere] text-sm leading-5 font-semibold text-gray-900 dark:text-gray-100">
                @if ($homeParticipant)
                    @if ($knockout->type === \App\KnockoutType::Doubles)
                        @php($homePlayers = [$homeParticipant->playerOne, $homeParticipant->playerTwo])
                        @foreach ($homePlayers as $player)
                            @if (! $loop->first)
                                <span class="text-gray-300 dark:text-zinc-600"> / </span>
                            @endif
                            @if ($player)
                                <a href="{{ route('player.show', $player) }}" class="transition hover:text-gray-500 dark:hover:text-gray-300">
                                    {{ $player->name }}
                                </a>
                            @else
                                <span>{{ $loop->first ? $homeLabel : 'TBC' }}</span>
                            @endif
                        @endforeach
                    @elseif ($homeParticipant->playerOne)
                        <a href="{{ route('player.show', $homeParticipant->playerOne) }}" class="transition hover:text-gray-500 dark:hover:text-gray-300">
                            {{ $homeLabel }}
                        </a>
                    @elseif ($homeParticipant->team)
                        <a href="{{ route('team.show', $homeParticipant->team) }}" class="transition hover:text-gray-500 dark:hover:text-gray-300">
                            {{ $homeLabel }}
                        </a>
                    @else
                        <span>{{ $homeLabel }}</span>
                    @endif
                @else
                    <span>{{ $homeLabel }}</span>
                @endif

                <span class="px-1 font-normal text-gray-400 dark:text-gray-500">vs</span>

                @if ($awayParticipant)
                    @if ($knockout->type === \App\KnockoutType::Doubles)
                        @php($awayPlayers = [$awayParticipant->playerOne, $awayParticipant->playerTwo])
                        @foreach ($awayPlayers as $player)
                            @if (! $loop->first)
                                <span class="text-gray-300 dark:text-zinc-600"> / </span>
                            @endif
                            @if ($player)
                                <a href="{{ route('player.show', $player) }}" class="transition hover:text-gray-500 dark:hover:text-gray-300">
                                    {{ $player->name }}
                                </a>
                            @else
                                <span>{{ $loop->first ? $awayLabel : 'TBC' }}</span>
                            @endif
                        @endforeach
                    @elseif ($awayParticipant->playerOne)
                        <a href="{{ route('player.show', $awayParticipant->playerOne) }}" class="transition hover:text-gray-500 dark:hover:text-gray-300">
                            {{ $awayLabel }}
                        </a>
                    @elseif ($awayParticipant->team)
                        <a href="{{ route('team.show', $awayParticipant->team) }}" class="transition hover:text-gray-500 dark:hover:text-gray-300">
                            {{ $awayLabel }}
                        </a>
                    @else
                        <span>{{ $awayLabel }}</span>
                    @endif
                @else
                    <span>{{ $awayLabel }}</span>
                @endif
            </p>

            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs leading-5 text-gray-500 dark:text-gray-400">
                @if ($matchLabel)
                    <span data-knockout-match-label>{{ $matchLabel }}</span>
                @endif

                @if (! $hasBye)
                    <span class="text-gray-300 dark:text-zinc-600">/</span>
                    @if ($match->venue)
                        <a href="{{ route('venue.show', $match->venue) }}"
                            class="max-w-full truncate transition hover:text-gray-700 dark:hover:text-gray-300"
                            title="{{ $match->venue->name }}">
                            {{ $match->venue->name }}
                        </a>
                    @else
                        <span>Venue TBC</span>
                    @endif
                @endif

                @if ($match->referee)
                    <span class="text-gray-300 dark:text-zinc-600">/</span>
                    <span>Referee: {{ $match->referee }}</span>
                @endif
            </div>
        </div>

        <div class="ml-auto flex shrink-0 self-center items-center text-right" data-knockout-score-state>
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
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $match->starts_at->format('j M') }}</p>
            @else
                <span class="inline-flex h-7 min-w-[60px] items-center justify-center rounded-full bg-gray-100 px-3 text-xs font-semibold uppercase tracking-wide text-gray-400 ring-1 ring-gray-200 dark:bg-zinc-700 dark:text-gray-500 dark:ring-zinc-700">
                    Vs
                </span>
            @endif
        </div>
    </div>
</article>
