@php
    $homeScore = (int) data_get($form->frames, $row['number'].'.home_score', 0);
    $awayScore = (int) data_get($form->frames, $row['number'].'.away_score', 0);

    $homeScoreBadgeClasses = 'ui-fixture-badge-neutral';
    $awayScoreBadgeClasses = 'ui-fixture-badge-neutral';

    if ($homeScore === 1 && $awayScore === 0) {
        $homeScoreBadgeClasses = 'ui-live-score-badge-win';
        $awayScoreBadgeClasses = 'ui-live-score-badge-loss';
    } elseif ($homeScore === 0 && $awayScore === 1) {
        $homeScoreBadgeClasses = 'ui-live-score-badge-loss';
        $awayScoreBadgeClasses = 'ui-live-score-badge-win';
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
    class="ui-result-form-frame-item transition-colors duration-1000"
    wire:key="result-frame-{{ $row['number'] }}"
    x-data="resultFormFlashRow({{ $row['number'] }})"
    x-on:result-frames-synced.window="flashIfIncluded($event.detail.frameNumbers ?? [])"
    :class="isFlashing ? 'bg-gray-100 dark:bg-neutral-900/80' : ''"
>
    <div class="ui-result-form-frame-content">
        <p class="ui-result-form-frame-label">Frame {{ $row['number'] }}</p>

        <div class="ui-result-form-player-row">
            <div
                class="ui-result-form-player-field transition-shadow duration-150"
                data-result-field-group
                data-result-field-key="{{ $row['number'] }}.home_player_id"
                x-bind:style="resultFieldActivityStyle('{{ $row['number'] }}.home_player_id')"
                x-data="resultFormPresenceTooltip('{{ $row['number'] }}.home_player_id')"
                x-ref="trigger"
                x-on:mouseenter="showTooltip()"
                x-on:mouseleave="hideTooltip()"
                x-on:focusin="showTooltip()"
                x-on:focusout="hideTooltip()"
            >
            @if ($row['home_selected_player'])
                <div
                    class="size-7 shrink-0"
                    wire:key="result-frame-{{ $row['number'] }}-home-avatar-{{ $row['home_selected_player']->id }}"
                >
                    <img
                        class="size-7 rounded-full object-cover"
                        src="{{ $row['home_selected_player']->avatar_url }}"
                        alt="{{ $row['home_selected_player']->name }} avatar"
                        style="animation: result-avatar-fade-in 300ms ease-out;"
                    >
                </div>
            @elseif ($row['home_is_awarded'])
                <div
                    class="size-7 shrink-0"
                    wire:key="result-frame-{{ $row['number'] }}-home-avatar-awarded"
                >
                    <img
                        class="size-7 rounded-full object-cover"
                        src="{{ asset('/images/user.jpg') }}"
                        alt="Awarded"
                        style="animation: result-avatar-fade-in 300ms ease-out;"
                    >
                </div>
            @else
                <div class="size-7 shrink-0 rounded-full bg-muted ring-1 ring-border"></div>
            @endif

                <select
                    wire:model.live="form.frames.{{ $row['number'] }}.home_player_id"
                    data-result-frame-field
                    data-frame-number="{{ $row['number'] }}"
                    data-frame-side="home"
                    data-frame-value="player"
                    data-result-field-key="{{ $row['number'] }}.home_player_id"
                    x-on:focus="beginResultFieldEditing($event)"
                    x-on:blur="endResultFieldEditing($event)"
                    x-bind:disabled="isResultFieldDisabled('{{ $row['number'] }}.home_player_id')"
                    x-bind:title="resultFieldLockLabel('{{ $row['number'] }}.home_player_id')"
                    class="min-w-0 flex-1 border-0 bg-transparent px-0 py-0 text-sm leading-5 text-neutral-950 focus:outline-0 focus:ring-0 dark:text-neutral-50 dark:[color-scheme:dark]"
                    @disabled($isLocked || ! $canEdit)
                >
                <option value="">Select player...</option>
                <option value="0">Awarded</option>
                @foreach ($fixture->homeTeam->players as $player)
                    <option value="{{ $player->id }}">{{ $player->name }}</option>
                @endforeach
                </select>

                <template x-teleport="body">
                    <div
                        x-cloak
                        x-show="open && isLockedField()"
                        x-ref="tooltip"
                        class="fixed z-[100] max-w-[min(18rem,calc(100vw-1rem))] rounded-xl px-2.5 py-1 text-center text-xs font-medium break-words shadow-md transition-opacity duration-150"
                        :style="`${tooltipStyle}; ${tooltipColorStyle(tooltipColor())} opacity:${isPositioned ? '1' : '0'}; pointer-events:none;`"
                        x-text="tooltipLabel()"
                    ></div>
                </template>
            </div>

            <div class="shrink-0">
                <div
                    class="ui-result-form-score-field ui-fixture-badge {{ $homeScoreBadgeClasses }} transition-shadow duration-150"
                    data-result-field-group
                    data-result-field-key="{{ $row['number'] }}.home_score"
                    x-bind:style="resultFieldActivityStyle('{{ $row['number'] }}.home_score')"
                    x-data="resultFormPresenceTooltip('{{ $row['number'] }}.home_score')"
                    x-ref="trigger"
                    x-on:mouseenter="showTooltip()"
                    x-on:mouseleave="hideTooltip()"
                    x-on:focusin="showTooltip()"
                    x-on:focusout="hideTooltip()"
                >
                    <select
                        wire:model.live="form.frames.{{ $row['number'] }}.home_score"
                        name="form.frames.{{ $row['number'] }}.home_score"
                        data-result-frame-field
                        data-frame-number="{{ $row['number'] }}"
                        data-frame-side="home"
                        data-frame-value="score"
                        data-result-field-key="{{ $row['number'] }}.home_score"
                        x-on:focus="beginResultFieldEditing($event)"
                        x-on:blur="endResultFieldEditing($event)"
                        x-bind:disabled="isResultFieldDisabled('{{ $row['number'] }}.home_score')"
                        x-bind:title="resultFieldLockLabel('{{ $row['number'] }}.home_score')"
                        class="block h-8 w-full appearance-none border-0 bg-transparent bg-none px-0 py-0 text-center text-xs font-semibold text-inherit [background-image:none] [text-align-last:center] focus:outline-0 focus:ring-0 dark:[color-scheme:dark]"
                        @disabled($isLocked || ! $canEdit)
                    >
                        <option value="0">0</option>
                        <option value="1">1</option>
                    </select>

                    <template x-teleport="body">
                        <div
                            x-cloak
                            x-show="open && isLockedField()"
                            x-ref="tooltip"
                            class="fixed z-[100] max-w-[min(18rem,calc(100vw-1rem))] rounded-xl px-2.5 py-1 text-center text-xs font-medium break-words shadow-md transition-opacity duration-150"
                            :style="`${tooltipStyle}; ${tooltipColorStyle(tooltipColor())} opacity:${isPositioned ? '1' : '0'}; pointer-events:none;`"
                            x-text="tooltipLabel()"
                        ></div>
                    </template>
                </div>
            </div>
        </div>

        <div class="ui-result-form-player-row">
            <div
                class="ui-result-form-player-field transition-shadow duration-150"
                data-result-field-group
                data-result-field-key="{{ $row['number'] }}.away_player_id"
                x-bind:style="resultFieldActivityStyle('{{ $row['number'] }}.away_player_id')"
                x-data="resultFormPresenceTooltip('{{ $row['number'] }}.away_player_id')"
                x-ref="trigger"
                x-on:mouseenter="showTooltip()"
                x-on:mouseleave="hideTooltip()"
                x-on:focusin="showTooltip()"
                x-on:focusout="hideTooltip()"
            >
            @if ($row['away_selected_player'])
                <div
                    class="size-7 shrink-0"
                    wire:key="result-frame-{{ $row['number'] }}-away-avatar-{{ $row['away_selected_player']->id }}"
                >
                    <img
                        class="size-7 rounded-full object-cover"
                        src="{{ $row['away_selected_player']->avatar_url }}"
                        alt="{{ $row['away_selected_player']->name }} avatar"
                        style="animation: result-avatar-fade-in 300ms ease-out;"
                    >
                </div>
            @elseif ($row['away_is_awarded'])
                <div
                    class="size-7 shrink-0"
                    wire:key="result-frame-{{ $row['number'] }}-away-avatar-awarded"
                >
                    <img
                        class="size-7 rounded-full object-cover"
                        src="{{ asset('/images/user.jpg') }}"
                        alt="Awarded"
                        style="animation: result-avatar-fade-in 300ms ease-out;"
                    >
                </div>
            @else
                <div class="size-7 shrink-0 rounded-full bg-muted ring-1 ring-border"></div>
            @endif

                <select
                    wire:model.live="form.frames.{{ $row['number'] }}.away_player_id"
                    data-result-frame-field
                    data-frame-number="{{ $row['number'] }}"
                    data-frame-side="away"
                    data-frame-value="player"
                    data-result-field-key="{{ $row['number'] }}.away_player_id"
                    x-on:focus="beginResultFieldEditing($event)"
                    x-on:blur="endResultFieldEditing($event)"
                    x-bind:disabled="isResultFieldDisabled('{{ $row['number'] }}.away_player_id')"
                    x-bind:title="resultFieldLockLabel('{{ $row['number'] }}.away_player_id')"
                    class="min-w-0 flex-1 border-0 bg-transparent px-0 py-0 text-sm leading-5 text-neutral-950 focus:outline-0 focus:ring-0 dark:text-neutral-50 dark:[color-scheme:dark]"
                    @disabled($isLocked || ! $canEdit)
                >
                <option value="">Select player...</option>
                <option value="0">Awarded</option>
                @foreach ($fixture->awayTeam->players as $player)
                    <option value="{{ $player->id }}">{{ $player->name }}</option>
                @endforeach
                </select>

                <template x-teleport="body">
                    <div
                        x-cloak
                        x-show="open && isLockedField()"
                        x-ref="tooltip"
                        class="fixed z-[100] max-w-[min(18rem,calc(100vw-1rem))] rounded-xl px-2.5 py-1 text-center text-xs font-medium break-words shadow-md transition-opacity duration-150"
                        :style="`${tooltipStyle}; ${tooltipColorStyle(tooltipColor())} opacity:${isPositioned ? '1' : '0'}; pointer-events:none;`"
                        x-text="tooltipLabel()"
                    ></div>
                </template>
            </div>

            <div class="shrink-0">
                <div
                    class="ui-result-form-score-field ui-fixture-badge {{ $awayScoreBadgeClasses }} transition-shadow duration-150"
                    data-result-field-group
                    data-result-field-key="{{ $row['number'] }}.away_score"
                    x-bind:style="resultFieldActivityStyle('{{ $row['number'] }}.away_score')"
                    x-data="resultFormPresenceTooltip('{{ $row['number'] }}.away_score')"
                    x-ref="trigger"
                    x-on:mouseenter="showTooltip()"
                    x-on:mouseleave="hideTooltip()"
                    x-on:focusin="showTooltip()"
                    x-on:focusout="hideTooltip()"
                >
                    <select
                        wire:model.live="form.frames.{{ $row['number'] }}.away_score"
                        name="form.frames.{{ $row['number'] }}.away_score"
                        data-result-frame-field
                        data-frame-number="{{ $row['number'] }}"
                        data-frame-side="away"
                        data-frame-value="score"
                        data-result-field-key="{{ $row['number'] }}.away_score"
                        x-on:focus="beginResultFieldEditing($event)"
                        x-on:blur="endResultFieldEditing($event)"
                        x-bind:disabled="isResultFieldDisabled('{{ $row['number'] }}.away_score')"
                        x-bind:title="resultFieldLockLabel('{{ $row['number'] }}.away_score')"
                        class="block h-8 w-full appearance-none border-0 bg-transparent bg-none px-0 py-0 text-center text-xs font-semibold text-inherit [background-image:none] [text-align-last:center] focus:outline-0 focus:ring-0 dark:[color-scheme:dark]"
                        @disabled($isLocked || ! $canEdit)
                    >
                        <option value="0">0</option>
                        <option value="1">1</option>
                    </select>

                    <template x-teleport="body">
                        <div
                            x-cloak
                            x-show="open && isLockedField()"
                            x-ref="tooltip"
                            class="fixed z-[100] max-w-[min(18rem,calc(100vw-1rem))] rounded-xl px-2.5 py-1 text-center text-xs font-medium break-words shadow-md transition-opacity duration-150"
                            :style="`${tooltipStyle}; ${tooltipColorStyle(tooltipColor())} opacity:${isPositioned ? '1' : '0'}; pointer-events:none;`"
                            x-text="tooltipLabel()"
                        ></div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
