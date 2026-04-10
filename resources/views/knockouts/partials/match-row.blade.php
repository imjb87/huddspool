<div class="ui-card-row items-start sm:items-center" data-knockout-match-row data-section-fixtures-band>
    <div class="min-w-0 flex-1">
        @if ($matchRow->match_label)
            <p class="mb-1 text-xs leading-5 text-gray-500 dark:text-gray-400">
                <span data-knockout-match-label>{{ $matchRow->match_label }}</span>
            </p>
        @endif

            @if (count($matchRow->home_parts) > 1 && count($matchRow->away_parts) > 1)
                <div class="space-y-0.5 text-sm leading-5 font-semibold">
                    <p class="[overflow-wrap:anywhere] {{ $matchRow->home_label_classes }}">
                        <span>{{ $matchRow->home_label }}</span>
                        <span class="px-1 font-normal text-gray-400 dark:text-gray-500">vs</span>
                    </p>
                    <p class="[overflow-wrap:anywhere] {{ $matchRow->away_label_classes }}">
                        {{ $matchRow->away_label }}
                    </p>
                </div>
            @else
                <p class="[overflow-wrap:anywhere] text-sm leading-5 font-semibold">
                    @foreach ($matchRow->home_parts as $part)
                        @if ($part['url'])
                            <a href="{{ $part['url'] }}" class="transition {{ $matchRow->home_label_classes }} hover:text-gray-500 dark:hover:text-gray-300">
                                {{ $part['label'] }}
                            </a>
                        @else
                            <span class="{{ $matchRow->home_label_classes }}">{{ $part['label'] }}</span>
                        @endif
                    @endforeach

                    <span class="px-1 font-normal text-gray-400 dark:text-gray-500">vs</span>

                    @foreach ($matchRow->away_parts as $part)
                        @if ($part['url'])
                            <a href="{{ $part['url'] }}" class="transition {{ $matchRow->away_label_classes }} hover:text-gray-500 dark:hover:text-gray-300">
                                {{ $part['label'] }}
                            </a>
                        @else
                            <span class="{{ $matchRow->away_label_classes }}">{{ $part['label'] }}</span>
                        @endif
                    @endforeach
                </p>
            @endif

        @if (! $matchRow->has_bye)
            <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">
                <span>Venue: </span>
                @if ($matchRow->match->venue)
                    <a href="{{ route('venue.show', $matchRow->match->venue) }}"
                        class="transition hover:text-gray-700 dark:hover:text-gray-300"
                        title="{{ $matchRow->match->venue->name }}">
                        {{ $matchRow->match->venue->name }}
                    </a>
                @else
                    <span>Venue TBC</span>
                @endif
            </p>

            <p class="text-xs leading-5 text-gray-500 dark:text-gray-400">
                {{ $matchRow->match->startsAtForDisplay()?->format('j F Y \\a\\t H:i') ?? 'Date TBC' }}
            </p>
        @endif

        @if ($matchRow->match->referee)
            <p class="text-xs leading-5 text-gray-500 dark:text-gray-400">
                <span>Referee: {{ $matchRow->match->referee }}</span>
            </p>
        @endif
    </div>

    <div class="ml-auto flex shrink-0 self-center items-center text-right" data-knockout-score-state>
        @if ($matchRow->match->forfeitParticipant)
            <span class="ui-score-pill-chip ui-score-pill-neutral">
                FF
            </span>
        @elseif ($matchRow->match->home_score !== null && $matchRow->match->away_score !== null)
            @php
                $homeSegmentClasses = $matchRow->match->home_score === $matchRow->match->away_score
                    ? 'ui-score-pill-segment-draw'
                    : ($matchRow->match->home_score > $matchRow->match->away_score ? 'ui-score-pill-segment-win' : 'ui-score-pill-segment-loss');
                $awaySegmentClasses = $matchRow->match->home_score === $matchRow->match->away_score
                    ? 'ui-score-pill-segment-draw'
                    : ($matchRow->match->away_score > $matchRow->match->home_score ? 'ui-score-pill-segment-win' : 'ui-score-pill-segment-loss');
            @endphp
            <span class="ui-score-pill ui-score-pill-neutral ui-score-pill-split"
                data-knockout-score-pill>
                <span class="ui-score-pill-segment {{ $homeSegmentClasses }} pl-1">
                    {{ $matchRow->match->home_score }}
                </span>
                <span class="ui-score-pill-divider-neutral"></span>
                <span class="ui-score-pill-segment {{ $awaySegmentClasses }} pr-1">
                    {{ $matchRow->match->away_score }}
                </span>
            </span>
        @elseif ($matchRow->match->starts_at)
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $matchRow->match->startsAtForDisplay()?->format('j M') }}</p>
        @else
            <span class="ui-score-pill-chip ui-score-pill-neutral">
                Vs
            </span>
        @endif
    </div>
</div>
