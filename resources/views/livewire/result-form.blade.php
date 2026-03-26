<form
    class="space-y-6"
    wire:submit.prevent="submit"
    x-data="{
        ...resultFormCollaboration({
            componentId: @js($this->getId()),
            channelName: @js($this->broadcastChannelName()),
            clientId: @js($clientId),
        }),
        ...resultFormEditors(@js($collaborators)),
    }"
    x-init="initEditors(); init()"
    data-result-form
>
    @if (! $isLocked)
        <div>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    <span x-text="collaboratorsUi.length === 1 ? '1 person is here' : `${collaboratorsUi.length} people are here`"></span>
                </p>

                <div
                    class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold"
                    data-result-form-connection-status
                    :class="statusClassName(connectionHealth, {
                        healthy: 'border-green-200 bg-green-50 text-green-700 dark:border-green-800/80 dark:bg-green-900/20 dark:text-green-300',
                        weak: 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-800/80 dark:bg-amber-900/20 dark:text-amber-300',
                        lost: 'border-red-200 bg-red-50 text-red-700 dark:border-red-800/80 dark:bg-red-900/20 dark:text-red-300',
                    })"
                >
                    <span
                        class="h-2.5 w-2.5 rounded-full"
                        aria-hidden="true"
                        :class="statusClassName(connectionHealth, {
                            healthy: 'bg-green-500',
                            weak: 'bg-amber-500',
                            lost: 'bg-red-500',
                        })"
                    ></span>
                    <span x-text="connectionBadgeText">Live updates connected</span>
                </div>
            </div>
            <div class="mt-3 flex items-center" wire:ignore>
                <div class="isolate flex -space-x-3">
                    <template x-for="collaborator in collaboratorsUi" :key="collaborator.id">
                        <div
                            class="relative transition duration-200 ease-out"
                            x-data="resultFormPresenceTooltip()"
                            x-show="collaborator.isVisible"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            x-on:mouseenter="showTooltip()"
                            x-on:mouseleave="hideTooltip()"
                            x-on:focusin="showTooltip()"
                            x-on:focusout="hideTooltip()"
                            x-on:click.prevent="showTooltip()"
                            x-on:click.outside="hideTooltip()"
                        >
                            <button
                                type="button"
                                class="relative block rounded-full ring-2 ring-white transition hover:-translate-y-0.5 focus:outline-hidden focus:ring-2 focus:ring-green-700 focus:ring-offset-2 focus:ring-offset-gray-50 dark:ring-zinc-900 dark:focus:ring-offset-zinc-900"
                                :aria-label="collaborator.name"
                                x-ref="trigger"
                            >
                                <img
                                    :src="collaborator.avatar_url"
                                    :alt="`${collaborator.name} avatar`"
                                    class="h-9 w-9 rounded-full object-cover"
                                >
                            </button>

                            <template x-teleport="body">
                                <div
                                    x-cloak
                                    x-show="open"
                                    x-ref="tooltip"
                                    class="fixed z-[100] max-w-[min(18rem,calc(100vw-1rem))] -translate-x-1/2 -translate-y-full rounded-xl bg-gray-900 px-2.5 py-1 text-center text-xs font-medium text-white break-words shadow-sm transition-opacity duration-150 dark:bg-zinc-100 dark:text-zinc-900"
                                    :style="`${tooltipStyle}; opacity:${isPositioned ? '1' : '0'}; pointer-events:${isPositioned ? 'auto' : 'none'};`"
                                    x-text="collaborator.name"
                                ></div>
                            </template>
                        </div>
                    </template>
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

            <div
                x-cloak
                x-show="connectionHealth !== 'healthy'"
                class="mt-4 rounded-2xl border px-4 py-3"
                data-result-form-connection-alert
                :class="statusClassName(connectionHealth, {
                    healthy: 'border-green-200 bg-green-50/80 text-green-800 dark:border-green-800/80 dark:bg-green-900/20 dark:text-green-200',
                    weak: 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-800/80 dark:bg-amber-900/20 dark:text-amber-200',
                    lost: 'border-red-200 bg-red-50 text-red-800 dark:border-red-800/80 dark:bg-red-900/20 dark:text-red-200',
                })"
            >
                <p class="text-sm font-semibold" x-text="connectionHeading">Weak connection detected</p>
                <p class="mt-1 text-sm leading-6" x-text="connectionMessage">Live updates may be delayed. It’s best if one person updates the result until your connection improves.</p>
            </div>
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
