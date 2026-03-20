<div class="py-4" wire:key="result-frame-{{ $row['number'] }}">
    <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">Frame {{ $row['number'] }}</p>

    <div class="space-y-3">
        <div class="flex items-center gap-3">
            @if ($row['home_selected_player'])
                <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                    src="{{ $row['home_selected_player']->avatar_url }}"
                    alt="{{ $row['home_selected_player']->name }} avatar">
            @elseif ($row['home_is_awarded'])
                <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                    src="{{ asset('/images/user.jpg') }}"
                    alt="Awarded">
            @else
                <div class="h-6 w-6 shrink-0 rounded-full bg-gray-100 ring-1 ring-gray-200 dark:bg-zinc-700 dark:ring-zinc-700"></div>
            @endif

            <select
                wire:model.live="form.frames.{{ $row['number'] }}.home_player_id"
                class="min-w-0 flex-1 border-0 bg-transparent px-0 py-0 text-sm leading-6 text-gray-900 focus:outline-0 focus:ring-0 dark:text-gray-100 dark:[color-scheme:dark]"
                @disabled($isLocked || ! $canEdit)
            >
                <option value="">Select player...</option>
                <option value="0">Awarded</option>
                @foreach ($fixture->homeTeam->players as $player)
                    <option value="{{ $player->id }}">{{ $player->name }}</option>
                @endforeach
            </select>

            <div class="shrink-0">
                <div class="inline-flex h-7 w-9 overflow-hidden rounded-full bg-gray-100 text-center text-xs font-extrabold text-gray-700 ring-1 ring-gray-200 dark:bg-zinc-700 dark:text-gray-200 dark:ring-zinc-700">
                    <select
                        wire:model.live="form.frames.{{ $row['number'] }}.home_score"
                        name="form.frames.{{ $row['number'] }}.home_score"
                        class="block h-7 w-full appearance-none border-0 bg-transparent bg-none px-0 py-0 text-center text-xs font-extrabold text-gray-700 [background-image:none] [text-align-last:center] focus:outline-0 focus:ring-0 dark:text-gray-200 dark:[color-scheme:dark]"
                        @disabled($isLocked || ! $canEdit)
                    >
                        <option value="0">0</option>
                        <option value="1">1</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if ($row['away_selected_player'])
                <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                    src="{{ $row['away_selected_player']->avatar_url }}"
                    alt="{{ $row['away_selected_player']->name }} avatar">
            @elseif ($row['away_is_awarded'])
                <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                    src="{{ asset('/images/user.jpg') }}"
                    alt="Awarded">
            @else
                <div class="h-6 w-6 shrink-0 rounded-full bg-gray-100 ring-1 ring-gray-200 dark:bg-zinc-700 dark:ring-zinc-700"></div>
            @endif

            <select
                wire:model.live="form.frames.{{ $row['number'] }}.away_player_id"
                class="min-w-0 flex-1 border-0 bg-transparent px-0 py-0 text-sm leading-6 text-gray-900 focus:outline-0 focus:ring-0 dark:text-gray-100 dark:[color-scheme:dark]"
                @disabled($isLocked || ! $canEdit)
            >
                <option value="">Select player...</option>
                <option value="0">Awarded</option>
                @foreach ($fixture->awayTeam->players as $player)
                    <option value="{{ $player->id }}">{{ $player->name }}</option>
                @endforeach
            </select>

            <div class="shrink-0">
                <div class="inline-flex h-7 w-9 overflow-hidden rounded-full bg-gray-100 text-center text-xs font-extrabold text-gray-700 ring-1 ring-gray-200 dark:bg-zinc-700 dark:text-gray-200 dark:ring-zinc-700">
                    <select
                        wire:model.live="form.frames.{{ $row['number'] }}.away_score"
                        name="form.frames.{{ $row['number'] }}.away_score"
                        class="block h-7 w-full appearance-none border-0 bg-transparent bg-none px-0 py-0 text-center text-xs font-extrabold text-gray-700 [background-image:none] [text-align-last:center] focus:outline-0 focus:ring-0 dark:text-gray-200 dark:[color-scheme:dark]"
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
