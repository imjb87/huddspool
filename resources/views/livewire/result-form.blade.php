<form
    class="space-y-6"
    wire:submit.prevent="submit"
    x-data="resultFormCollaboration({
        componentId: @js($this->getId()),
        channelName: @js($this->broadcastChannelName()),
        clientId: @js($clientId),
    })"
    x-init="init()"
    data-result-form
>
    @if (! $isLocked)
        <div>
            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Editing now</p>
            <div class="mt-3 flex items-center">
                <div class="isolate flex -space-x-3">
                @foreach ($collaborators as $collaborator)
                    <div
                        class="relative"
                        x-data="{ open: false }"
                        x-on:mouseenter="open = true"
                        x-on:mouseleave="open = false"
                        x-on:focusin="open = true"
                        x-on:focusout="open = false"
                        x-on:click.prevent="open = true"
                        x-on:click.outside="open = false"
                    >
                        <button
                            type="button"
                            class="relative block rounded-full ring-2 ring-white transition hover:-translate-y-0.5 focus:outline-hidden focus:ring-2 focus:ring-green-700 focus:ring-offset-2 focus:ring-offset-gray-50 dark:ring-zinc-900 dark:focus:ring-offset-zinc-900"
                            aria-label="{{ $collaborator['name'] }}"
                        >
                            <img
                                src="{{ $collaborator['avatar_url'] }}"
                                alt="{{ $collaborator['name'] }} avatar"
                                class="h-9 w-9 rounded-full object-cover"
                            >
                        </button>

                        <div
                            x-cloak
                            x-show="open"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            class="absolute bottom-full left-1/2 z-10 mb-2 -translate-x-1/2 whitespace-nowrap rounded-full bg-gray-900 px-2.5 py-1 text-xs font-medium text-white shadow-sm dark:bg-zinc-100 dark:text-zinc-900"
                        >
                            {{ $collaborator['name'] }}
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
            @if ($lastEditedAt)
                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                    Last edited
                    @if ($lastUpdatedByName)
                        by {{ $lastUpdatedByName }}
                    @endif
                    {{ $lastEditedAt }}
                </p>
            @endif
        </div>
    @endif

    <div class="space-y-4" data-result-form-shell>
        <div class="divide-y divide-gray-200 dark:divide-zinc-800/80" data-result-form-frames>
            @foreach ($frameRows as $row)
                @include('livewire.result-form-partials.frame-row', ['row' => $row])
            @endforeach
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
                Result submitted
            </div>
        @endif
    </div>
</form>
