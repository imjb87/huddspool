<div class="ui-knockout-match-item" data-knockout-match-row data-section-fixtures-band data-slot="item" data-variant="muted" data-size="default">
    <div class="ui-knockout-match-content" data-slot="item-content">
        <div class="ui-knockout-match-details">
            @if ($matchRow->match_label)
                <p class="ui-knockout-match-label">
                    <span data-knockout-match-label>{{ $matchRow->match_label }}</span>
                </p>
            @endif

            <div class="ui-knockout-team-matchup">
                <div class="ui-knockout-team-names">
                    @if (count($matchRow->home_parts) > 1 && count($matchRow->away_parts) > 1)
                        <p class="ui-knockout-team-name {{ $matchRow->home_label_classes }}">{{ $matchRow->home_label }}</p>
                        <p class="ui-knockout-team-name {{ $matchRow->away_label_classes }}">{{ $matchRow->away_label }}</p>
                    @else
                        <p class="ui-knockout-team-name">
                            @foreach ($matchRow->home_parts as $part)
                                @if ($part['url'])
                                    <a href="{{ $part['url'] }}" class="transition {{ $matchRow->home_label_classes }} hover:text-gray-500 dark:hover:text-gray-300">
                                        {{ $part['label'] }}
                                    </a>
                                @else
                                    <span class="{{ $matchRow->home_label_classes }}">{{ $part['label'] }}</span>
                                @endif
                            @endforeach
                        </p>

                        <p class="ui-knockout-team-name">
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
                </div>

                <div class="ui-knockout-match-actions" data-knockout-score-state data-slot="item-actions">
                    @if ($matchRow->match->forfeitParticipant)
                        <span class="ui-fixture-badge ui-fixture-badge-neutral" data-slot="badge">
                            FF
                        </span>
                    @elseif ($matchRow->match->home_score !== null && $matchRow->match->away_score !== null)
                        @php
                            $homeBadgeClasses = $matchRow->match->home_score === $matchRow->match->away_score
                                ? 'ui-live-score-badge-draw'
                                : ($matchRow->match->home_score > $matchRow->match->away_score ? 'ui-live-score-badge-win' : 'ui-live-score-badge-loss');
                            $awayBadgeClasses = $matchRow->match->home_score === $matchRow->match->away_score
                                ? 'ui-live-score-badge-draw'
                                : ($matchRow->match->away_score > $matchRow->match->home_score ? 'ui-live-score-badge-win' : 'ui-live-score-badge-loss');
                        @endphp
                        <div class="ui-fixture-badge-stack" role="group" aria-label="{{ $matchRow->match->home_score }} to {{ $matchRow->match->away_score }}" data-knockout-score-pill>
                            <span class="ui-fixture-badge {{ $homeBadgeClasses }}" data-slot="badge">{{ $matchRow->match->home_score }}</span>
                            <span class="ui-fixture-badge {{ $awayBadgeClasses }}" data-slot="badge">{{ $matchRow->match->away_score }}</span>
                        </div>
                    @elseif ($matchRow->match->starts_at)
                        <span class="ui-fixture-badge ui-fixture-badge-neutral" data-slot="badge" aria-label="Match date {{ $matchRow->match->startsAtForDisplay()?->format('j M') }}">
                            {{ $matchRow->match->startsAtForDisplay()?->format('j M') }}
                        </span>
                    @else
                        <span class="ui-fixture-badge ui-fixture-badge-neutral" data-slot="badge">
                            Vs
                        </span>
                    @endif
                </div>
            </div>

            @if (! $matchRow->has_bye)
                @if ($matchRow->match->venue || $matchRow->match->knockout?->type !== \App\KnockoutType::Singles)
                    <p class="ui-knockout-match-meta mt-1">
                        <span>Venue: </span>
                        @if ($matchRow->match->venue)
                            <a href="{{ route('venue.show', $matchRow->match->venue) }}"
                                class="transition hover:text-neutral-700 dark:hover:text-neutral-200"
                                title="{{ $matchRow->match->venue->name }}">
                                {{ $matchRow->match->venue->name }}
                            </a>
                        @else
                            <span>Venue TBC</span>
                        @endif
                    </p>
                @endif

            @endif

            @if ($matchRow->match->referee)
                <p class="ui-knockout-match-meta mt-1">
                    <span>Referee: {{ $matchRow->match->referee }}</span>
                </p>
            @endif
        </div>
    </div>
</div>
