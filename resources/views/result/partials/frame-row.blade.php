<div class="ui-card-row items-start" wire:key="result-frame-{{ $frame->id }}">
    <div class="min-w-0 w-full flex-1 space-y-3" data-result-card-band>
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Frame {{ $index + 1 }}
        </p>

        @include('result.partials.frame-side', [
            'playerId' => $frame->home_player_id,
            'player' => $frame->homePlayer,
            'score' => $frame->home_score,
            'opponentScore' => $frame->away_score,
        ])

        @include('result.partials.frame-side', [
            'playerId' => $frame->away_player_id,
            'player' => $frame->awayPlayer,
            'score' => $frame->away_score,
            'opponentScore' => $frame->home_score,
        ])
    </div>
</div>
