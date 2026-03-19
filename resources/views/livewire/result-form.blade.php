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

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-zinc-800/80 dark:bg-zinc-800/75 dark:shadow-none dark:ring-1 dark:ring-white/5">
        <div class="hidden bg-linear-to-br from-green-900 via-green-800 to-green-700 sm:flex">
            <div class="flex-1 leading-6 py-2 px-4 text-left font-semibold text-white text-sm">
                {{ $fixture->homeTeam->name }}
            </div>
            <div class="w-12 text-center leading-6 py-2 font-semibold text-white text-sm">
                vs
            </div>
            <div class="flex-1 leading-6 py-2 px-4 text-right font-semibold text-white text-sm">
                {{ $fixture->awayTeam->name }}
            </div>
        </div>
        @for ($i = 1; $i <= 10; $i++)
            <div class="flex flex-wrap" wire:key="result-frame-{{ $i }}">
                <div class="w-full sm:w-auto flex sm:flex-1 order-2 sm:order-first border-b border-gray-200 dark:border-zinc-800/80 sm:border-0">
                    <select
                        wire:model.live="form.frames.{{ $i }}.home_player_id"
                        class="flex-1 border-0 bg-transparent py-2 px-4 text-sm leading-6 text-gray-900 focus:outline-0 focus:ring-0 dark:text-gray-100 dark:[color-scheme:dark]"
                        @disabled($isLocked || ! $canEdit)
                    >
                        <option value="">Select player...</option>
                        <option value="0">Awarded</option>
                        @foreach ($fixture->homeTeam->players as $player)
                            <option value="{{ $player->id }}">{{ $player->name }}</option>
                        @endforeach
                    </select>
                    <div class="w-10 sm:w-12 border-x border-gray-200 dark:border-zinc-800/80">
                        <select
                            wire:model.live="form.frames.{{ $i }}.home_score"
                            name="form.frames.{{ $i }}.home_score"
                            class="block w-full appearance-none border-0 bg-transparent pr-0 pl-0 py-2 text-center text-sm leading-6 text-gray-900 [text-align-last:center] focus:outline-0 focus:ring-0 dark:text-gray-100 dark:[color-scheme:dark]"
                            placeholder="0"
                            @disabled($isLocked || ! $canEdit)
                        >
                            <option value="0">0</option>
                            <option value="1">1</option>
                        </select>
                    </div>
                </div>
                <div
                    class="order-first w-full bg-gray-50 py-2 px-4 text-left text-sm font-semibold leading-6 text-gray-900 dark:bg-zinc-700 dark:text-gray-100 sm:w-12 sm:px-0 sm:text-center"
                >
                    <span class="sm:hidden">Frame </span>
                    {{ $i }}
                </div>
                <div class="w-full sm:w-auto flex sm:flex-1 order-last">
                    <div class="w-10 sm:w-12 order-last sm:order-first border-x border-gray-200 dark:border-zinc-800/80">
                        <select
                        wire:model.live="form.frames.{{ $i }}.away_score"
                        name="form.frames.{{ $i }}.away_score"
                        class="block w-full appearance-none border-0 bg-transparent pr-0 pl-0 py-2 text-center text-sm leading-6 text-gray-900 [text-align-last:center] focus:outline-0 focus:ring-0 dark:text-gray-100 dark:[color-scheme:dark]"
                        placeholder="0"
                        @disabled($isLocked || ! $canEdit)
                    >
                        <option value="0">0</option>
                        <option value="1">1</option>
                    </select>
                </div>
                <select
                    wire:model.live="form.frames.{{ $i }}.away_player_id"
                    class="order-first flex-1 border-0 bg-transparent py-2 px-4 text-sm leading-6 text-gray-900 focus:outline-0 focus:ring-0 dark:text-gray-100 dark:[color-scheme:dark] sm:order-last"
                    @disabled($isLocked || ! $canEdit)
                >
                    <option value="">Select player...</option>
                    <option value="0">Awarded</option>
                    @foreach ($fixture->awayTeam->players as $player)
                        <option value="{{ $player->id }}">{{ $player->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endfor
        <div class="flex flex-wrap border-t border-gray-200 bg-gray-50 text-sm font-semibold text-gray-900 dark:border-zinc-800/80 dark:bg-zinc-800/70 dark:text-gray-100">
            <div class="w-full sm:w-auto flex sm:flex-1 border-b border-gray-200 dark:border-zinc-800/80">
                <div class="flex-1 leading-6 py-2 px-4 sm:text-right">
                    Home total
                </div>
                    <div class="w-10 sm:w-12 leading-6 py-2 text-center border-x border-gray-200 dark:border-zinc-800/80">
                    {{ $form->homeScore }}
                    </div>
                </div>
            <div class="w-10 sm:w-12 bg-gray-50 dark:bg-zinc-800/70"></div>
            <div class="w-full sm:w-auto flex sm:flex-1">
                <div class="w-10 sm:w-12 order-last border-x border-gray-200 py-2 text-center leading-6 dark:border-zinc-800/80 sm:order-first">
                    {{ $form->awayScore }}
                </div>
                <div class="flex-1 leading-6 py-2 px-4 order-first sm:order-last">
                    Away total
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
