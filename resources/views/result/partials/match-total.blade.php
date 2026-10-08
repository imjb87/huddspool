@php
    $isDraw = $result->home_score === $result->away_score;
    $homeBadgeClasses = $isDraw
        ? 'ui-live-score-badge-draw'
        : ($result->home_score > $result->away_score ? 'ui-live-score-badge-win' : 'ui-live-score-badge-loss');
    $awayBadgeClasses = $isDraw
        ? 'ui-live-score-badge-draw'
        : ($result->away_score > $result->home_score ? 'ui-live-score-badge-win' : 'ui-live-score-badge-loss');
@endphp

<div class="ui-result-total-item" data-result-card-band>
    <p class="ui-result-frame-label">Match total</p>

    <div class="ui-fixture-team-matchup">
        <div class="ui-fixture-team-names">
            <p class="ui-fixture-team-name">{{ $result->home_team_name }}</p>
            <p class="ui-fixture-team-name">{{ $result->away_team_name }}</p>
        </div>

        <div class="ui-fixture-item-actions self-center" data-slot="item-actions">
            <div class="ui-fixture-badge-stack" data-result-score-pill role="group" aria-label="{{ $result->home_team_name }} {{ $result->home_score }} to {{ $result->away_score }} {{ $result->away_team_name }}">
                <span class="ui-fixture-badge {{ $homeBadgeClasses }}">{{ $result->home_score }}</span>
                <span class="ui-fixture-badge {{ $awayBadgeClasses }}">{{ $result->away_score }}</span>
            </div>
        </div>
    </div>
</div>
