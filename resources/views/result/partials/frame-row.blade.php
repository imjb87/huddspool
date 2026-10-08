<div class="ui-result-frame-item" wire:key="result-frame-{{ $frame->id }}" data-slot="item" data-variant="muted" data-size="default">
    <div class="ui-result-frame-content" data-result-card-band>
        <p class="ui-result-frame-label">
            Frame {{ $index + 1 }}
        </p>

        <div class="flex flex-col gap-2">
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
</div>
