@if ($knockoutRows->isNotEmpty())
    <section class="ui-section" data-player-knockout-section>
        <div class="ui-shell-grid">
            <div class="ui-section-intro gap-2">
                <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-tournament size-5 text-neutral-700 dark:text-neutral-200">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M2 4a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                        <path d="M18 10a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                        <path d="M2 12a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                        <path d="M2 20a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                        <path d="M6 12h3a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-3" />
                        <path d="M6 4h7a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-2" />
                        <path d="M14 10h4" />
                    </svg>
                </span>
                <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                    <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Knockouts</h2>
                    <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                        See where this player has appeared in the knockout bracket.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card ui-knockout-round-card">
                    <div class="ui-knockout-match-group">
                        @foreach ($knockoutRows as $knockoutRow)
                            @php
                                $hasScore = $knockoutRow->has_result
                                    && $knockoutRow->home_score !== null
                                    && $knockoutRow->away_score !== null;
                                $isDraw = $hasScore && (int) $knockoutRow->home_score === (int) $knockoutRow->away_score;
                                $homeBadgeClasses = $isDraw
                                    ? 'ui-live-score-badge-draw'
                                    : ((int) $knockoutRow->home_score > (int) $knockoutRow->away_score
                                        ? 'ui-live-score-badge-win'
                                        : 'ui-live-score-badge-loss');
                                $awayBadgeClasses = $isDraw
                                    ? 'ui-live-score-badge-draw'
                                    : ((int) $knockoutRow->away_score > (int) $knockoutRow->home_score
                                        ? 'ui-live-score-badge-win'
                                        : 'ui-live-score-badge-loss');
                            @endphp

                            @if ($knockoutRow->row_url)
                                <a href="{{ $knockoutRow->row_url }}" class="ui-knockout-match-item" wire:key="player-knockout-{{ $knockoutRow->id }}" data-slot="item" data-variant="muted" data-size="default">
                            @else
                                <div class="ui-knockout-match-item" wire:key="player-knockout-{{ $knockoutRow->id }}" data-slot="item" data-variant="muted" data-size="default">
                            @endif
                                    <div class="ui-knockout-match-content" data-slot="item-content">
                                        <div class="ui-knockout-match-details">
                                            <p class="ui-knockout-match-label">{{ $knockoutRow->meta_label }}</p>

                                            <div class="ui-knockout-team-matchup">
                                                <div class="ui-knockout-team-names">
                                                    <p class="ui-knockout-team-name">{{ $knockoutRow->home_label }}</p>
                                                    <p class="ui-knockout-team-name">{{ $knockoutRow->away_label }}</p>
                                                </div>

                                                <div class="ui-knockout-match-actions" data-slot="item-actions">
                                                    @if ($hasScore)
                                                        <div class="ui-fixture-badge-stack" role="group" aria-label="{{ $knockoutRow->home_label }} {{ $knockoutRow->home_score }} to {{ $knockoutRow->away_score }} {{ $knockoutRow->away_label }}">
                                                            <span class="ui-fixture-badge {{ $homeBadgeClasses }}" data-slot="badge">{{ $knockoutRow->home_score }}</span>
                                                            <span class="ui-fixture-badge {{ $awayBadgeClasses }}" data-slot="badge">{{ $knockoutRow->away_score }}</span>
                                                        </div>
                                                    @else
                                                        <span class="ui-fixture-badge ui-fixture-badge-neutral" data-slot="badge">{{ $knockoutRow->date_label }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            @if ($knockoutRow->row_url)
                                </a>
                            @else
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
