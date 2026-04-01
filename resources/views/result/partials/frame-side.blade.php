@php
    $scorePillClasses = 'bg-gray-100 text-gray-700 ring-1 ring-gray-200 dark:bg-neutral-800 dark:text-gray-200 dark:ring-neutral-800';

    if ((int) $score === 1 && (int) $opponentScore === 0) {
        $scorePillClasses = 'bg-linear-to-br from-green-900 via-green-800 to-green-700 text-white ring-1 ring-black/10';
    } elseif ((int) $score === 0 && (int) $opponentScore === 1) {
        $scorePillClasses = 'bg-linear-to-br from-red-900 via-red-800 to-red-700 text-white ring-1 ring-black/10';
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
        <div class="inline-flex h-7 w-9 items-center justify-center overflow-hidden rounded-full text-center text-xs font-extrabold {{ $scorePillClasses }}"
            data-result-frame-score-pill>
            {{ $score }}
        </div>
    </div>
</div>
