@php
    $scorePillClasses = 'ui-score-pill-neutral';

    if ((int) $score === 1 && (int) $opponentScore === 0) {
        $scorePillClasses = 'ui-score-pill-success';
    } elseif ((int) $score === 0 && (int) $opponentScore === 1) {
        $scorePillClasses = 'ui-score-pill-danger';
    }
@endphp

<div class="grid w-full grid-cols-[minmax(0,1fr)_auto] items-center gap-3">
    <div class="min-w-0">
        @if ($playerId)
            <a href="{{ route('player.show', $player) }}"
                class="flex min-w-0 items-center gap-2 text-sm font-medium text-gray-900 transition hover:text-gray-500 dark:text-gray-100 dark:hover:text-gray-300">
                <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                    src="{{ $player->avatar_url }}"
                    alt="{{ $player->name }} avatar">
                <span class="truncate">{{ $player->name }}</span>
            </a>
        @else
            <span class="flex min-w-0 items-center gap-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                    src="{{ asset('/images/user.jpg') }}"
                    alt="Awarded">
                <span class="truncate">Awarded</span>
            </span>
        @endif
    </div>

    <div class="shrink-0 justify-self-end">
        <div class="ui-score-pill ui-score-pill-single {{ $scorePillClasses }}"
            data-result-frame-score-pill>
            {{ $score }}
        </div>
    </div>
</div>
