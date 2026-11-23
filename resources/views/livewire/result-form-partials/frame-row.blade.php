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

<div
    class="ui-result-form-frame-item"
    wire:key="result-frame-{{ $row['number'] }}"
    x-data="resultFormFlashRow({{ $row['number'] }})"
    x-on:result-frames-synced.window="flashIfIncluded($event.detail.frameNumbers ?? [])"
    :class="isFlashing ? 'ui-result-form-frame-item--updated' : ''"
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
                <div class="size-7 shrink-0" wire:key="result-frame-{{ $row['number'] }}-home-avatar">
                    <img
                        x-cloak
                        x-show="playerPickerSelectionAvatar('{{ $row['number'] }}.home_player_id')"
                        x-bind:src="playerPickerSelectionAvatar('{{ $row['number'] }}.home_player_id')"
                        x-bind:alt="`${playerPickerSelectionLabel('{{ $row['number'] }}.home_player_id')} avatar`"
                        class="ui-result-avatar size-7 rounded-full object-cover"
                        src="{{ $row['home_selected_player']?->avatar_url ?? asset('/images/user.jpg') }}"
                        alt="{{ $row['home_selected_player']?->name ?? ($row['home_is_awarded'] ? 'Awarded' : '') }} avatar"
                    >
                    <div
                        x-show="!playerPickerSelectionAvatar('{{ $row['number'] }}.home_player_id')"
                        class="size-7 rounded-full bg-muted ring-1 ring-border"
                    ></div>
                </div>

                <button
                    type="button"
                    data-player-picker-trigger
                    data-result-player-picker-action="open"
                    data-result-field-control
                    data-result-field-key="{{ $row['number'] }}.home_player_id"
                    data-frame-number="{{ $row['number'] }}"
                    data-player-side="home"
                    aria-haspopup="dialog"
                    aria-label="Choose home player for frame {{ $row['number'] }}"
                    x-bind:aria-expanded="playerPickerOpen && playerPickerFieldKey === '{{ $row['number'] }}.home_player_id'"
                    x-on:focus="beginResultFieldEditing($event)"
                    x-on:blur="endResultFieldEditing($event)"
                    x-bind:disabled="isResultFieldDisabled('{{ $row['number'] }}.home_player_id') || formLocked || !formCanEdit"
                    x-bind:title="resultFieldLockLabel('{{ $row['number'] }}.home_player_id')"
                    class="flex min-w-0 flex-1 items-center justify-between gap-2 border-0 bg-transparent px-0 py-0 text-left text-sm leading-5 text-neutral-950 focus:outline-0 focus:ring-0 disabled:cursor-not-allowed disabled:opacity-60 dark:text-neutral-50"
                    @disabled($isLocked || ! $canEdit)
                >
                    <span
                        class="min-w-0 truncate"
                        x-text="playerPickerSelectionLabel('{{ $row['number'] }}.home_player_id')"
                        x-bind:class="playerPickerSelectionLabel('{{ $row['number'] }}.home_player_id') === 'Select player...' ? 'text-neutral-500 dark:text-neutral-400' : ''"
                    >{{ $row['home_selected_player']?->name ?? ($row['home_is_awarded'] ? 'Awarded' : 'Select player...') }}</span>
                    <svg class="size-4 shrink-0 text-neutral-500 dark:text-neutral-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m7 10 5 5 5-5" />
                    </svg>
                </button>

                <select
                    wire:model.live="form.frames.{{ $row['number'] }}.home_player_id"
                    data-result-frame-field
                    data-result-field-control
                    data-frame-number="{{ $row['number'] }}"
                    data-frame-side="home"
                    data-frame-value="player"
                    data-result-field-key="{{ $row['number'] }}.home_player_id"
                    x-on:focus="beginResultFieldEditing($event)"
                    x-on:blur="endResultFieldEditing($event)"
                    x-on:change="syncPlayerPickerSelection($event); touchResultFieldLock($event)"
                    x-bind:disabled="isResultFieldDisabled('{{ $row['number'] }}.home_player_id')"
                    x-bind:title="resultFieldLockLabel('{{ $row['number'] }}.home_player_id')"
                    class="hidden"
                    tabindex="-1"
                    aria-hidden="true"
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

                <div class="ui-result-form-score-control">
                    <label
                        class="ui-result-form-score-field ui-fixture-badge {{ $homeScoreBadgeClasses }} cursor-pointer transition-shadow duration-150"
                        for="result-frame-{{ $row['number'] }}-home-score"
                        data-result-field-control
                        data-result-field-key="{{ $row['number'] }}.home_player_id"
                        wire:click.prevent="toggleFrameScore({{ $row['number'] }}, 'home')"
                    >
                        <input
                            id="result-frame-{{ $row['number'] }}-home-score"
                            type="radio"
                            name="frame-{{ $row['number'] }}-winner"
                            value="1"
                            data-result-frame-field
                            data-frame-number="{{ $row['number'] }}"
                            data-frame-side="home"
                            data-frame-value="score"
                            @checked($homeScore === 1 && $awayScore === 0)
                            x-bind:disabled="isResultFieldDisabled('{{ $row['number'] }}.home_player_id') || formLocked || !formCanEdit"
                            data-result-field-control
                            data-result-field-key="{{ $row['number'] }}.home_player_id"
                            class="sr-only"
                            @disabled($isLocked || ! $canEdit)
                            aria-label="Home wins frame {{ $row['number'] }}"
                        >
                        <span aria-hidden="true">{{ $homeScore }}</span>
                    </label>
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
                <div class="size-7 shrink-0" wire:key="result-frame-{{ $row['number'] }}-away-avatar">
                    <img
                        x-cloak
                        x-show="playerPickerSelectionAvatar('{{ $row['number'] }}.away_player_id')"
                        x-bind:src="playerPickerSelectionAvatar('{{ $row['number'] }}.away_player_id')"
                        x-bind:alt="`${playerPickerSelectionLabel('{{ $row['number'] }}.away_player_id')} avatar`"
                        class="ui-result-avatar size-7 rounded-full object-cover"
                        src="{{ $row['away_selected_player']?->avatar_url ?? asset('/images/user.jpg') }}"
                        alt="{{ $row['away_selected_player']?->name ?? ($row['away_is_awarded'] ? 'Awarded' : '') }} avatar"
                    >
                    <div
                        x-show="!playerPickerSelectionAvatar('{{ $row['number'] }}.away_player_id')"
                        class="size-7 rounded-full bg-muted ring-1 ring-border"
                    ></div>
                </div>

                <button
                    type="button"
                    data-player-picker-trigger
                    data-result-player-picker-action="open"
                    data-result-field-control
                    data-result-field-key="{{ $row['number'] }}.away_player_id"
                    data-frame-number="{{ $row['number'] }}"
                    data-player-side="away"
                    aria-haspopup="dialog"
                    aria-label="Choose away player for frame {{ $row['number'] }}"
                    x-bind:aria-expanded="playerPickerOpen && playerPickerFieldKey === '{{ $row['number'] }}.away_player_id'"
                    x-on:focus="beginResultFieldEditing($event)"
                    x-on:blur="endResultFieldEditing($event)"
                    x-bind:disabled="isResultFieldDisabled('{{ $row['number'] }}.away_player_id') || formLocked || !formCanEdit"
                    x-bind:title="resultFieldLockLabel('{{ $row['number'] }}.away_player_id')"
                    class="flex min-w-0 flex-1 items-center justify-between gap-2 border-0 bg-transparent px-0 py-0 text-left text-sm leading-5 text-neutral-950 focus:outline-0 focus:ring-0 disabled:cursor-not-allowed disabled:opacity-60 dark:text-neutral-50"
                    @disabled($isLocked || ! $canEdit)
                >
                    <span
                        class="min-w-0 truncate"
                        x-text="playerPickerSelectionLabel('{{ $row['number'] }}.away_player_id')"
                        x-bind:class="playerPickerSelectionLabel('{{ $row['number'] }}.away_player_id') === 'Select player...' ? 'text-neutral-500 dark:text-neutral-400' : ''"
                    >{{ $row['away_selected_player']?->name ?? ($row['away_is_awarded'] ? 'Awarded' : 'Select player...') }}</span>
                    <svg class="size-4 shrink-0 text-neutral-500 dark:text-neutral-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m7 10 5 5 5-5" />
                    </svg>
                </button>

                <select
                    wire:model.live="form.frames.{{ $row['number'] }}.away_player_id"
                    data-result-frame-field
                    data-result-field-control
                    data-frame-number="{{ $row['number'] }}"
                    data-frame-side="away"
                    data-frame-value="player"
                    data-result-field-key="{{ $row['number'] }}.away_player_id"
                    x-on:focus="beginResultFieldEditing($event)"
                    x-on:blur="endResultFieldEditing($event)"
                    x-on:change="syncPlayerPickerSelection($event); touchResultFieldLock($event)"
                    x-bind:disabled="isResultFieldDisabled('{{ $row['number'] }}.away_player_id')"
                    x-bind:title="resultFieldLockLabel('{{ $row['number'] }}.away_player_id')"
                    class="hidden"
                    tabindex="-1"
                    aria-hidden="true"
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

                <div class="ui-result-form-score-control">
                    <label
                        class="ui-result-form-score-field ui-fixture-badge {{ $awayScoreBadgeClasses }} cursor-pointer transition-shadow duration-150"
                        for="result-frame-{{ $row['number'] }}-away-score"
                        data-result-field-control
                        data-result-field-key="{{ $row['number'] }}.away_player_id"
                        wire:click.prevent="toggleFrameScore({{ $row['number'] }}, 'away')"
                    >
                        <input
                            id="result-frame-{{ $row['number'] }}-away-score"
                            type="radio"
                            name="frame-{{ $row['number'] }}-winner"
                            value="1"
                            data-result-frame-field
                            data-frame-number="{{ $row['number'] }}"
                            data-frame-side="away"
                            data-frame-value="score"
                            @checked($awayScore === 1 && $homeScore === 0)
                            x-bind:disabled="isResultFieldDisabled('{{ $row['number'] }}.away_player_id') || formLocked || !formCanEdit"
                            data-result-field-control
                            data-result-field-key="{{ $row['number'] }}.away_player_id"
                            class="sr-only"
                            @disabled($isLocked || ! $canEdit)
                            aria-label="Away wins frame {{ $row['number'] }}"
                        >
                        <span aria-hidden="true">{{ $awayScore }}</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
