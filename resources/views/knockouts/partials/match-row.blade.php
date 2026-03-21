<article class="py-4" data-knockout-match-row>
    <div class="flex items-start justify-between gap-4" data-section-fixtures-band>
        <div class="min-w-0 flex-1">
            <p class="[overflow-wrap:anywhere] text-sm leading-5 font-semibold text-gray-900 dark:text-gray-100">
                @foreach ($matchRow->home_parts as $part)
                    @if (! $loop->first)
                        <span class="text-gray-300 dark:text-zinc-600"> / </span>
                    @endif
                    @if ($part['url'])
                        <a href="{{ $part['url'] }}" class="transition hover:text-gray-500 dark:hover:text-gray-300">
                            {{ $part['label'] }}
                        </a>
                    @else
                        <span>{{ $part['label'] }}</span>
                    @endif
                @endforeach

                <span class="px-1 font-normal text-gray-400 dark:text-gray-500">vs</span>

                @foreach ($matchRow->away_parts as $part)
                    @if (! $loop->first)
                        <span class="text-gray-300 dark:text-zinc-600"> / </span>
                    @endif
                    @if ($part['url'])
                        <a href="{{ $part['url'] }}" class="transition hover:text-gray-500 dark:hover:text-gray-300">
                            {{ $part['label'] }}
                        </a>
                    @else
                        <span>{{ $part['label'] }}</span>
                    @endif
                @endforeach
            </p>

            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs leading-5 text-gray-500 dark:text-gray-400">
                @if ($matchRow->match_label)
                    <span data-knockout-match-label>{{ $matchRow->match_label }}</span>
                @endif

                @if (! $matchRow->has_bye)
                    <span class="text-gray-300 dark:text-zinc-600">/</span>
                    @if ($matchRow->match->venue)
                        <a href="{{ route('venue.show', $matchRow->match->venue) }}"
                            class="max-w-full truncate transition hover:text-gray-700 dark:hover:text-gray-300"
                            title="{{ $matchRow->match->venue->name }}">
                            {{ $matchRow->match->venue->name }}
                        </a>
                    @else
                        <span>Venue TBC</span>
                    @endif
                @endif

                @if ($matchRow->match->referee)
                    <span class="text-gray-300 dark:text-zinc-600">/</span>
                    <span>Referee: {{ $matchRow->match->referee }}</span>
                @endif
            </div>
        </div>

        <div class="ml-auto flex shrink-0 self-center items-center text-right" data-knockout-score-state>
            @if ($matchRow->match->forfeitParticipant)
                <span class="inline-flex h-7 min-w-[60px] items-center justify-center rounded-full bg-gray-100 px-3 text-xs font-bold uppercase tracking-wide text-gray-600 ring-1 ring-gray-200 dark:bg-zinc-700 dark:text-gray-300 dark:ring-zinc-700">
                    FF
                </span>
            @elseif ($matchRow->match->home_score !== null && $matchRow->match->away_score !== null)
                <span class="inline-flex h-7 w-[60px] overflow-hidden rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10"
                    data-knockout-score-pill>
                    <span class="flex w-1/2 items-center justify-center tabular-nums pl-1">
                        {{ $matchRow->match->home_score }}
                    </span>
                    <span class="w-px bg-white/25"></span>
                    <span class="flex w-1/2 items-center justify-center tabular-nums pr-1">
                        {{ $matchRow->match->away_score }}
                    </span>
                </span>
            @elseif ($matchRow->match->starts_at)
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $matchRow->match->starts_at->format('j M') }}</p>
            @else
                <span class="inline-flex h-7 min-w-[60px] items-center justify-center rounded-full bg-gray-100 px-3 text-xs font-semibold uppercase tracking-wide text-gray-400 ring-1 ring-gray-200 dark:bg-zinc-700 dark:text-gray-500 dark:ring-zinc-700">
                    Vs
                </span>
            @endif
        </div>
    </div>
</article>
