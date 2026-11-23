<div
    x-cloak
    x-show="playerPickerOpen"
    x-on:keydown.escape.stop.prevent="closePlayerPicker()"
    x-on:keydown.tab="trapPlayerPickerFocus($event)"
    :aria-hidden="!playerPickerOpen"
    :inert="!playerPickerOpen"
    class="fixed inset-x-0 top-0 z-[70] flex items-stretch justify-center sm:items-center sm:p-4"
    x-bind:style="playerPickerViewportStyle"
    role="dialog"
    aria-modal="true"
    aria-labelledby="result-player-picker-title"
    data-result-player-picker-dialog
>
    <div
        class="absolute inset-0 bg-black/20 transition-opacity dark:bg-black/60"
        x-show="playerPickerOpen"
        x-transition:enter="ui-motion-fade-in"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ui-motion-fade-out"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        aria-hidden="true"
        data-result-player-picker-action="close"
    ></div>

    <div
        x-ref="playerPickerDialog"
        x-show="playerPickerOpen"
        x-transition:enter="ui-motion-search-shell-in"
        x-transition:leave="ui-motion-player-picker-shell-out"
        class="relative z-10 flex h-full w-full flex-col overflow-hidden bg-background text-foreground sm:h-[36rem] sm:max-h-[calc(100dvh-2rem)] sm:max-w-lg sm:rounded-xl sm:border sm:border-border/70 sm:bg-card sm:text-card-foreground sm:shadow-2xl sm:ring-1 sm:ring-foreground/10"
        data-result-player-picker-shell
    >
        <div class="flex shrink-0 items-center justify-between gap-4 border-b border-border/70 px-4 py-3">
            <div class="min-w-0">
                <h2 id="result-player-picker-title" class="truncate text-base font-semibold text-foreground">Choose a player</h2>
                <p class="mt-0.5 text-sm text-muted-foreground" x-text="playerPickerSide === 'home' ? 'Home team' : 'Away team'"></p>
            </div>

            <button
                type="button"
                class="inline-flex size-9 shrink-0 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                aria-label="Close player selection"
                data-result-player-picker-action="close"
                data-result-player-picker-close
            >
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M18 6 6 18M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="relative mx-3 mt-3 flex h-10 shrink-0 items-center rounded-md border border-border/70 bg-muted/50 px-3">
            <svg class="mr-2 size-4 shrink-0 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7" />
                <path d="m20 20-4-4" />
            </svg>
            <input
                type="search"
                x-ref="playerPickerSearch"
                x-model="playerPickerSearch"
                x-bind:disabled="playerPickerIsDisabled()"
                x-bind:data-result-field-key="playerPickerFieldKey"
                x-bind:title="resultFieldLockLabel(playerPickerFieldKey)"
                x-on:focus="beginResultFieldEditing($event)"
                x-on:blur="endResultFieldEditing($event)"
                x-on:keydown.arrow-down.prevent="focusFirstPlayerPickerOption()"
                x-on:keydown.enter.prevent
                class="h-10 min-w-0 flex-1 border-0 bg-transparent px-0 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:ring-0 disabled:cursor-not-allowed"
                placeholder="Search players..."
                aria-label="Search players"
                role="combobox"
                aria-autocomplete="list"
                x-bind:aria-expanded="playerPickerOpen"
                x-bind:aria-controls="playerPickerListId()"
                data-result-field-control
                data-result-player-picker-search
            >
        </div>

        <div class="flex shrink-0 items-center justify-between gap-3 px-3 py-3">
            <p class="text-xs text-muted-foreground">Frame <span x-text="playerPickerFrameNumber"></span></p>
            <button
                type="button"
                class="rounded-md px-2.5 py-1.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50"
                x-bind:disabled="playerPickerIsDisabled() || playerPickerSelectedId === ''"
                x-bind:data-result-field-key="playerPickerFieldKey"
                x-bind:title="resultFieldLockLabel(playerPickerFieldKey)"
                x-on:focus="beginResultFieldEditing($event)"
                x-on:blur="endResultFieldEditing($event)"
                data-result-player-picker-action="clear"
                data-result-field-control
                data-result-player-picker-clear
            >
                Clear selection
            </button>
        </div>

        <div
            x-ref="playerPickerOptions"
            class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-2 pb-2"
            role="listbox"
            x-bind:id="playerPickerListId()"
            aria-label="Available players"
            data-result-player-picker-options
        >
            <template x-for="player in filteredPlayerPickerPlayers()" :key="player.isAwarded ? 'awarded' : `player-${player.id}`">
                <button
                    type="button"
                    role="option"
                    class="flex min-h-12 w-full items-center gap-3 rounded-md px-3 py-2 text-left text-sm text-foreground outline-none transition-colors hover:bg-muted focus:bg-muted disabled:pointer-events-none disabled:opacity-50"
                    x-bind:aria-selected="String(player.id) === String(playerPickerSelectedId)"
                    x-bind:class="String(player.id) === String(playerPickerSelectedId) ? 'bg-muted' : ''"
                    x-bind:data-result-field-key="playerPickerFieldKey"
                    x-bind:data-player-id="player.id"
                    data-result-player-picker-action="select"
                    x-bind:disabled="playerPickerIsDisabled()"
                    x-on:focus="beginResultFieldEditing($event)"
                    x-on:blur="endResultFieldEditing($event)"
                    data-result-field-control
                    data-result-player-option
                >
                    <span class="relative flex size-9 shrink-0 overflow-hidden rounded-full bg-muted ring-1 ring-border/70" aria-hidden="true">
                        <img x-bind:src="player.avatarUrl" alt="" class="aspect-square size-full object-cover">
                    </span>
                    <span class="min-w-0 flex-1 truncate font-medium" x-bind:data-player-picker-name="player.id" x-text="player.name"></span>
                    <svg
                        x-cloak
                        x-show="String(player.id) === String(playerPickerSelectedId)"
                        class="size-4 shrink-0 text-foreground"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="m5 12 4 4L19 6" />
                    </svg>
                </button>
            </template>

            <p
                x-cloak
                x-show="filteredPlayerPickerPlayers().length === 0"
                class="px-3 py-8 text-center text-sm text-muted-foreground"
                data-result-player-picker-empty
            >
                No players match your search.
            </p>
        </div>
    </div>
</div>
