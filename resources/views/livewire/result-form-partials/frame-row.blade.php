@php
    $homeScore = (int) data_get($form->frames, $row['number'].'.home_score', 0);
    $awayScore = (int) data_get($form->frames, $row['number'].'.away_score', 0);

    $homeScorePillClasses = 'ui-score-pill-neutral';
    $awayScorePillClasses = 'ui-score-pill-neutral';

    if ($homeScore === 1 && $awayScore === 0) {
        $homeScorePillClasses = 'ui-score-pill-success';
        $awayScorePillClasses = 'ui-score-pill-danger';
    } elseif ($homeScore === 0 && $awayScore === 1) {
        $homeScorePillClasses = 'ui-score-pill-danger';
        $awayScorePillClasses = 'ui-score-pill-success';
    }
@endphp

@once
    <style>
        @keyframes result-avatar-fade-in {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>
@endonce

<div
    class="ui-card-row items-start transition-colors duration-1000"
    wire:key="result-frame-{{ $row['number'] }}"
    x-data="resultFormFlashRow({{ $row['number'] }})"
    x-on:result-frames-synced.window="flashIfIncluded($event.detail.frameNumbers ?? [])"
    :class="isFlashing ? 'bg-gray-100 dark:bg-neutral-900/80' : ''"
>
    <div class="min-w-0 w-full flex-1 space-y-3">
        <p class="text-xs text-gray-500 dark:text-gray-400">Frame {{ $row['number'] }}</p>

        <div class="grid w-full grid-cols-[minmax(0,1fr)_auto] items-center gap-3">
            <div class="min-w-0 flex items-center gap-3">
            @if ($row['home_selected_player'])
                <div
                    class="h-6 w-6 shrink-0"
                    wire:key="result-frame-{{ $row['number'] }}-home-avatar-{{ $row['home_selected_player']->id }}"
                >
                    <img
                        class="h-6 w-6 rounded-full object-cover"
                        src="{{ $row['home_selected_player']->avatar_url }}"
                        alt="{{ $row['home_selected_player']->name }} avatar"
                        style="animation: result-avatar-fade-in 300ms ease-out;"
                    >
                </div>
            @elseif ($row['home_is_awarded'])
                <div
                    class="h-6 w-6 shrink-0"
                    wire:key="result-frame-{{ $row['number'] }}-home-avatar-awarded"
                >
                    <img
                        class="h-6 w-6 rounded-full object-cover"
                        src="{{ asset('/images/user.jpg') }}"
                        alt="Awarded"
                        style="animation: result-avatar-fade-in 300ms ease-out;"
                    >
                </div>
            @else
                <div class="h-6 w-6 shrink-0 rounded-full bg-gray-100 ring-1 ring-gray-200 dark:bg-neutral-800 dark:ring-neutral-800"></div>
            @endif

            <select
                wire:model.live="form.frames.{{ $row['number'] }}.home_player_id"
                data-result-frame-field
                data-frame-number="{{ $row['number'] }}"
                data-frame-side="home"
                data-frame-value="player"
                class="min-w-0 flex-1 border-0 bg-transparent px-0 py-0 text-sm leading-6 text-gray-900 focus:outline-0 focus:ring-0 dark:text-gray-100 dark:[color-scheme:dark]"
                @disabled($isLocked || ! $canEdit)
            >
                <option value="">Select player...</option>
                <option value="0">Awarded</option>
                @foreach ($fixture->homeTeam->players as $player)
                    <option value="{{ $player->id }}">{{ $player->name }}</option>
                @endforeach
            </select>
            </div>

            <div class="shrink-0 justify-self-end">
                <div class="ui-score-pill ui-score-pill-single {{ $homeScorePillClasses }}">
                    <select
                        wire:model.live="form.frames.{{ $row['number'] }}.home_score"
                        name="form.frames.{{ $row['number'] }}.home_score"
                        data-result-frame-field
                        data-frame-number="{{ $row['number'] }}"
                        data-frame-side="home"
                        data-frame-value="score"
                        class="block h-7 w-full appearance-none border-0 bg-transparent bg-none px-0 py-0 text-center text-xs font-extrabold text-inherit [background-image:none] [text-align-last:center] focus:outline-0 focus:ring-0 dark:[color-scheme:dark]"
                        @disabled($isLocked || ! $canEdit)
                    >
                        <option value="0">0</option>
                        <option value="1">1</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid w-full grid-cols-[minmax(0,1fr)_auto] items-center gap-3">
            <div class="min-w-0 flex items-center gap-3">
            @if ($row['away_selected_player'])
                <div
                    class="h-6 w-6 shrink-0"
                    wire:key="result-frame-{{ $row['number'] }}-away-avatar-{{ $row['away_selected_player']->id }}"
                >
                    <img
                        class="h-6 w-6 rounded-full object-cover"
                        src="{{ $row['away_selected_player']->avatar_url }}"
                        alt="{{ $row['away_selected_player']->name }} avatar"
                        style="animation: result-avatar-fade-in 300ms ease-out;"
                    >
                </div>
            @elseif ($row['away_is_awarded'])
                <div
                    class="h-6 w-6 shrink-0"
                    wire:key="result-frame-{{ $row['number'] }}-away-avatar-awarded"
                >
                    <img
                        class="h-6 w-6 rounded-full object-cover"
                        src="{{ asset('/images/user.jpg') }}"
                        alt="Awarded"
                        style="animation: result-avatar-fade-in 300ms ease-out;"
                    >
                </div>
            @else
                <div class="h-6 w-6 shrink-0 rounded-full bg-gray-100 ring-1 ring-gray-200 dark:bg-neutral-800 dark:ring-neutral-800"></div>
            @endif

            <select
                wire:model.live="form.frames.{{ $row['number'] }}.away_player_id"
                data-result-frame-field
                data-frame-number="{{ $row['number'] }}"
                data-frame-side="away"
                data-frame-value="player"
                class="min-w-0 flex-1 border-0 bg-transparent px-0 py-0 text-sm leading-6 text-gray-900 focus:outline-0 focus:ring-0 dark:text-gray-100 dark:[color-scheme:dark]"
                @disabled($isLocked || ! $canEdit)
            >
                <option value="">Select player...</option>
                <option value="0">Awarded</option>
                @foreach ($fixture->awayTeam->players as $player)
                    <option value="{{ $player->id }}">{{ $player->name }}</option>
                @endforeach
            </select>
            </div>

            <div class="shrink-0 justify-self-end">
                <div class="ui-score-pill ui-score-pill-single {{ $awayScorePillClasses }}">
                    <select
                        wire:model.live="form.frames.{{ $row['number'] }}.away_score"
                        name="form.frames.{{ $row['number'] }}.away_score"
                        data-result-frame-field
                        data-frame-number="{{ $row['number'] }}"
                        data-frame-side="away"
                        data-frame-value="score"
                        class="block h-7 w-full appearance-none border-0 bg-transparent bg-none px-0 py-0 text-center text-xs font-extrabold text-inherit [background-image:none] [text-align-last:center] focus:outline-0 focus:ring-0 dark:[color-scheme:dark]"
                        @disabled($isLocked || ! $canEdit)
                    >
                        <option value="0">0</option>
                        <option value="1">1</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
