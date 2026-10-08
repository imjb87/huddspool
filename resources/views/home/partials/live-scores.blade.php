<section id="live-scores" class="ui-section" data-home-live-scores x-data="window.homeLiveScoresMotion()" x-init="init()">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-shell-grid">
            <div class="ui-section-intro gap-2">
                <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-bolt size-5 text-neutral-700 dark:text-neutral-200">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M13 3l0 7l6 0l-8 11l0 -7l-6 0l8 -11" />
                    </svg>
                </span>
                <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                    <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Live scores</h2>
                    <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                        See results as teams submit them during league night.
                    </p>
                </div>
            </div>
            <div class="lg:col-span-2">
                @if ($liveScores->isEmpty())
                    <div class="ui-card ui-live-scores-card">
                        <x-ui-empty-state
                            title="No live scores to show right now."
                            description="No scores have been submitted yet. Check back during league night to follow results as they come in."
                            data-home-live-scores-empty
                        />
                    </div>
                @else
                    <div class="ui-card ui-live-scores-card" data-home-live-scores-shell>
                        <div class="ui-live-scores-item-group max-h-80 overflow-y-auto overscroll-contain" data-home-live-scores-list x-ref="list">
                            @foreach ($liveScores as $result)
                                @php
                                    $homeBadgeClasses = $result->home_score === $result->away_score
                                        ? 'ui-live-score-badge-draw'
                                        : ($result->home_score > $result->away_score ? 'ui-live-score-badge-win' : 'ui-live-score-badge-loss');
                                    $awayBadgeClasses = $result->home_score === $result->away_score
                                        ? 'ui-live-score-badge-draw'
                                        : ($result->away_score > $result->home_score ? 'ui-live-score-badge-win' : 'ui-live-score-badge-loss');
                                @endphp
                                <a href="{{ $result->live_score_url }}" class="ui-card-row-link ui-live-score-item ui-live-score-item--enter" data-home-live-score-row data-home-live-score-key="{{ $result->id }}" data-slot="item" data-variant="muted" data-size="default">
                                    <div class="ui-live-score-item-content" data-slot="item-content">
                                        @if ($result->row_meta !== '')
                                            <p class="ui-live-score-section-name line-clamp-2 text-left" data-slot="item-description">{{ $result->row_meta }}</p>
                                        @endif

                                        <div class="ui-live-score-team-matchup">
                                            <div class="ui-live-score-team-names">
                                                <p class="ui-live-score-team-name sm:hidden">{{ $result->home_team_shortname ?: $result->home_team_name }}</p>
                                                <p class="ui-live-score-team-name hidden sm:block">{{ $result->home_team_name }}</p>
                                                <p class="ui-live-score-team-name sm:hidden">{{ $result->away_team_shortname ?: $result->away_team_name }}</p>
                                                <p class="ui-live-score-team-name hidden sm:block">{{ $result->away_team_name }}</p>
                                            </div>

                                            <div class="ui-live-score-item-actions" data-slot="item-actions">
                                                <div class="ui-live-score-badge-stack" role="group" aria-label="{{ $result->home_team_name }} {{ $result->home_score }} to {{ $result->away_score }} {{ $result->away_team_name }}" data-home-live-score-pill>
                                                    <span class="ui-live-score-badge {{ $homeBadgeClasses }}" data-slot="badge">{{ $result->home_score }}</span>
                                                    <span class="ui-live-score-badge {{ $awayBadgeClasses }}" data-slot="badge">{{ $result->away_score }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
