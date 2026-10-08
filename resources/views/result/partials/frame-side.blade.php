@php
    $scoreBadgeClasses = 'ui-fixture-badge-neutral';

    if ((int) $score === 1 && (int) $opponentScore === 0) {
        $scoreBadgeClasses = 'ui-live-score-badge-win';
    } elseif ((int) $score === 0 && (int) $opponentScore === 1) {
        $scoreBadgeClasses = 'ui-live-score-badge-loss';
    }
@endphp

<div class="ui-result-player-row">
    <div class="min-w-0">
        @if ($playerId)
            <a href="{{ route('player.show', $player) }}" class="ui-result-player-link">
                <img class="size-8 shrink-0 rounded-full object-cover"
                    src="{{ $player->avatar_url }}"
                    alt="{{ $player->name }} avatar">
                <span class="truncate text-sm font-medium">{{ $player->name }}</span>
            </a>
        @else
            <span class="ui-result-player-link">
                <img class="size-8 shrink-0 rounded-full object-cover"
                    src="{{ asset('/images/user.jpg') }}"
                    alt="Awarded">
                <span class="truncate text-sm font-medium">Awarded</span>
            </span>
        @endif
    </div>

    <div class="shrink-0">
        <span class="ui-fixture-badge {{ $scoreBadgeClasses }}" data-result-frame-score-pill>
            {{ $score }}
        </span>
    </div>
</div>
