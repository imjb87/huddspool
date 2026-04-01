@php
    $homeSegmentClasses = $result->home_score === $result->away_score
        ? 'ui-score-pill-segment-draw'
        : ($result->home_score > $result->away_score ? 'ui-score-pill-segment-win' : 'ui-score-pill-segment-loss');
    $awaySegmentClasses = $result->home_score === $result->away_score
        ? 'ui-score-pill-segment-draw'
        : ($result->away_score > $result->home_score ? 'ui-score-pill-segment-win' : 'ui-score-pill-segment-loss');
@endphp

<div class="ui-card-row items-start justify-between gap-4" data-result-card-band>
    <div class="min-w-0 flex-1">
        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Match total</p>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ $result->home_team_name }}
            <span class="text-gray-300 dark:text-neutral-600">/</span>
            {{ $result->away_team_name }}
        </p>
    </div>

    <div class="ml-auto flex shrink-0 self-center items-center text-right">
        <div class="ui-score-pill ui-score-pill-neutral ui-score-pill-split"
            data-result-score-pill>
            <div class="ui-score-pill-segment {{ $homeSegmentClasses }} pl-1">{{ $result->home_score }}</div>
            <div class="ui-score-pill-divider-neutral"></div>
            <div class="ui-score-pill-segment {{ $awaySegmentClasses }} pr-1">{{ $result->away_score }}</div>
        </div>
    </div>
</div>
