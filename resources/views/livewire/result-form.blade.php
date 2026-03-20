<form class="space-y-6" wire:submit.prevent="submit" wire:poll.60s="keepLockAlive" data-result-form>
    @if ($lockedByAnother && ! $isLocked)
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-200">
            This result is currently being updated by {{ $lockOwnerName ?? 'another team admin' }}. Lock expires {{ $lockExpiresAtHuman ?? 'soon' }}.
        </div>
    @elseif (! $isLocked && $canEdit)
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/60 dark:bg-green-950/30 dark:text-green-200">
            You have edit access. Your lock renews automatically while this page stays open.
        </div>
    @endif

    <div class="space-y-4" data-result-form-shell>
        <div class="divide-y divide-gray-200 dark:divide-zinc-800/80" data-result-form-frames>
            @for ($i = 1; $i <= 10; $i++)
                @php
                    $homeSelectedPlayer = $fixture->homeTeam->players->firstWhere('id', (int) data_get($form->frames, $i.'.home_player_id'));
                    $awaySelectedPlayer = $fixture->awayTeam->players->firstWhere('id', (int) data_get($form->frames, $i.'.away_player_id'));
                    $homeIsAwarded = (string) data_get($form->frames, $i.'.home_player_id') === '0';
                    $awayIsAwarded = (string) data_get($form->frames, $i.'.away_player_id') === '0';
                @endphp
                <div class="py-4" wire:key="result-frame-{{ $i }}">
                    <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">Frame {{ $i }}</p>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            @if ($homeSelectedPlayer)
                                <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                                    src="{{ $homeSelectedPlayer->avatar_url }}"
                                    alt="{{ $homeSelectedPlayer->name }} avatar">
                            @elseif ($homeIsAwarded)
                                <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                                    src="{{ asset('/images/user.jpg') }}"
                                    alt="Awarded">
                            @else
                                <div class="h-6 w-6 shrink-0 rounded-full bg-gray-100 ring-1 ring-gray-200 dark:bg-zinc-800 dark:ring-zinc-700"></div>
                            @endif

                            <select
                                wire:model.live="form.frames.{{ $i }}.home_player_id"
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
                                <div class="inline-flex h-7 w-9 overflow-hidden rounded-full bg-gray-100 text-center text-xs font-extrabold text-gray-700 ring-1 ring-gray-200 dark:bg-zinc-800 dark:text-gray-200 dark:ring-zinc-700">
                                    <select
                                        wire:model.live="form.frames.{{ $i }}.home_score"
                                        name="form.frames.{{ $i }}.home_score"
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
                            @if ($awaySelectedPlayer)
                                <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                                    src="{{ $awaySelectedPlayer->avatar_url }}"
                                    alt="{{ $awaySelectedPlayer->name }} avatar">
                            @elseif ($awayIsAwarded)
                                <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                                    src="{{ asset('/images/user.jpg') }}"
                                    alt="Awarded">
                            @else
                                <div class="h-6 w-6 shrink-0 rounded-full bg-gray-100 ring-1 ring-gray-200 dark:bg-zinc-800 dark:ring-zinc-700"></div>
                            @endif

                            <select
                                wire:model.live="form.frames.{{ $i }}.away_player_id"
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
                                <div class="inline-flex h-7 w-9 overflow-hidden rounded-full bg-gray-100 text-center text-xs font-extrabold text-gray-700 ring-1 ring-gray-200 dark:bg-zinc-800 dark:text-gray-200 dark:ring-zinc-700">
                                    <select
                                        wire:model.live="form.frames.{{ $i }}.away_score"
                                        name="form.frames.{{ $i }}.away_score"
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
            @endfor
        </div>

        <div class="flex items-start justify-between gap-4 py-1" data-result-form-band>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Match total</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ $fixture->homeTeam->name }}
                    <span class="text-gray-300 dark:text-zinc-600">/</span>
                    {{ $fixture->awayTeam->name }}
                </p>
            </div>

            <div class="ml-auto flex shrink-0 self-center items-center text-right">
                <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10">
                    <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">{{ $form->homeScore }}</div>
                    <div class="w-px bg-white/25"></div>
                    <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">{{ $form->awayScore }}</div>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <x-errors />
    @endif

    <div class="flex justify-end gap-x-3 pt-2">
        <a
            href="{{ route('fixture.show', $fixture->id) }}"
            class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-900 shadow-xs ring-1 ring-inset ring-slate-300 transition hover:bg-slate-50 dark:bg-zinc-900 dark:text-gray-100 dark:ring-zinc-700 dark:hover:bg-zinc-800"
        >
            Cancel
        </a>

        @if (! $isLocked && $canEdit)
            <button
                type="submit"
                class="inline-flex justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-4 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 transition hover:brightness-110 focus-visible:outline-solid focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700"
                wire:loading.attr="disabled"
                wire:target="submit"
            >
                Submit result
            </button>
        @elseif ($isLocked)
            <div class="flex items-center text-sm font-semibold text-green-700 dark:text-green-400">
                Result locked
            </div>
        @else
            <div class="flex items-center text-sm font-semibold text-amber-700 dark:text-amber-300">
                Editing locked by {{ $lockOwnerName ?? 'another team admin' }}
            </div>
        @endif
    </div>
</form>
