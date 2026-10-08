<form
    class="space-y-6 lg:contents"
    wire:submit.prevent="submit"
    x-data="{
        ...resultFormCollaboration({
            componentId: @js($this->getId()),
            channelName: @js($this->broadcastChannelName()),
            clientId: @js($clientId),
            collaboratorId: @js(auth()->id()),
            collaboratorName: @js(auth()->user()->name),
            collaboratorColor: @js(\App\Support\ResultFormCollaboratorColor::forUser((int) auth()->id())),
        }),
        ...resultFormEditors(
            @js($collaborators),
            @js(\App\Support\ResultFormCollaboratorColor::palette()),
        ),
        ...resultFormRecovery({
            componentId: @js($this->getId()),
            fixtureId: @js($fixture->getKey()),
            draftVersion: @js($draftVersion),
            isLocked: @js($isLocked),
        }),
    }"
    x-init="initEditors(); initRecovery(); init()"
    x-on:result-form-server-synced.window="syncSavedDraft($event.detail?.[0] ?? $event.detail)"
    data-result-form
>
    @if (! $isLocked)
        <section class="ui-section" data-result-form-presence-section>
            <div class="ui-shell-grid">
                <div class="ui-section-intro gap-2">
                    <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users-group size-5 text-neutral-700 dark:text-neutral-200">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                            <path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" />
                            <path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                            <path d="M17 10h2a2 2 0 0 1 2 2v1" />
                            <path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                            <path d="M3 13v-1a2 2 0 0 1 2 -2h2" />
                        </svg>
                    </span>
                    <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                        <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Presence</h2>
                        <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                            See who else is viewing this scorecard before you start editing.
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="ui-card">
                        <div class="ui-card-body">
                            <div class="hidden" aria-hidden="true">
                                @foreach ($collaborators as $collaborator)
                                    <button type="button" aria-label="{{ $collaborator['name'] }}"></button>
                                    <img src="{{ $collaborator['avatar_url'] }}" alt="{{ $collaborator['name'] }} avatar">
                                @endforeach
                            </div>

                            <div class="min-w-0" wire:ignore>
                                <p class="mb-3 text-xs text-neutral-500 dark:text-neutral-400">
                                    <span x-text="collaboratorsUi.length === 1 ? '1 person here' : `${collaboratorsUi.length} people here`"></span>
                                </p>

                                <div class="flex items-center justify-between gap-3">
                                    <div class="isolate flex items-center -space-x-3">
                                        <template x-for="collaborator in collaboratorsUi" :key="collaborator.id">
                                            <div
                                                class="relative flex items-center transition-[opacity,transform] duration-150 ease-out"
                                                x-data="resultFormPresenceTooltip()"
                                                x-show="collaborator.isVisible"
                                                x-transition:enter="ui-motion-popover-in"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                x-transition:leave="ui-motion-popover-out"
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
                                                    class="relative block rounded-full ring-2 ring-white transition-[box-shadow,transform] duration-150 hover:-translate-y-0.5 focus:outline-hidden focus:ring-2 focus:ring-green-700 focus:ring-offset-2 focus:ring-offset-white dark:ring-neutral-950 dark:focus:ring-offset-neutral-950"
                                                    :aria-label="collaborator.name"
                                                    :style="collaboratorActivityStyle(collaborator)"
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
                                                        class="fixed z-[100] max-w-[min(18rem,calc(100vw-1rem))] rounded-xl px-2.5 py-1 text-center text-xs font-medium break-words shadow-md transition-opacity duration-150"
                                                        :style="`${tooltipStyle}; ${tooltipColorStyle(collaborator.color)} opacity:${isPositioned ? '1' : '0'}; pointer-events:${isPositioned ? 'auto' : 'none'};`"
                                                        x-text="collaborator.name"
                                                    ></div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>

                                    <div
                                        class="relative flex shrink-0 items-center justify-end self-center"
                                        x-data="resultFormPresenceTooltip()"
                                        x-on:mouseenter="showTooltip()"
                                        x-on:mouseleave="hideTooltip()"
                                        x-on:focusin="showTooltip()"
                                        x-on:focusout="hideTooltip()"
                                    >
                                        <button
                                            type="button"
                                            class="inline-flex items-center justify-end"
                                            data-result-form-connection-status
                                            role="status"
                                            aria-live="polite"
                                            :aria-label="connectionBadgeText"
                                            :title="connectionBadgeText"
                                            x-ref="trigger"
                                        >
                                            <span
                                                class="inline-block h-3.5 w-3.5 rounded-full"
                                                aria-hidden="true"
                                                :class="statusClassName(connectionHealth, {
                                                    healthy: 'bg-green-500 shadow-[0_0_0_0.35rem_rgba(34,197,94,0.24)] animate-pulse dark:shadow-[0_0_0_0.45rem_rgba(34,197,94,0.28)]',
                                                    weak: 'bg-amber-500 shadow-[0_0_0_0.4rem_rgba(245,158,11,0.3)] dark:shadow-[0_0_0_0.5rem_rgba(245,158,11,0.35)]',
                                                    lost: 'bg-red-500 shadow-[0_0_0_0.4rem_rgba(239,68,68,0.3)] dark:shadow-[0_0_0_0.5rem_rgba(239,68,68,0.35)]',
                                                })"
                                            ></span>
                                            <span class="sr-only" x-text="connectionBadgeText">Live updates connected</span>
                                        </button>

                                        <template x-teleport="body">
                                            <div
                                                x-cloak
                                                x-show="open"
                                                x-ref="tooltip"
                                                class="fixed z-[100] max-w-[calc(100vw-1rem)] rounded-md bg-gray-900 px-2.5 py-1 text-center text-xs font-medium whitespace-nowrap text-white shadow-sm transition-opacity duration-150 dark:bg-neutral-100 dark:text-neutral-900"
                                                :style="`${tooltipStyle}; opacity:${isPositioned ? '1' : '0'}; pointer-events:${isPositioned ? 'auto' : 'none'};`"
                                                x-text="connectionBadgeText"
                                            ></div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div
            x-cloak
            x-show="connectionHealth !== 'healthy'"
            class="mx-auto w-full max-w-4xl rounded-lg border px-4 py-3 sm:px-6 lg:px-6"
            data-result-form-connection-alert
            :class="statusClassName(connectionHealth, {
                healthy: 'border-green-500/50 bg-green-50/50 text-green-900 dark:border-green-500/50 dark:bg-green-950/20 dark:text-green-200',
                weak: 'border-amber-500/50 bg-amber-50/50 text-amber-900 dark:border-amber-500/50 dark:bg-amber-950/20 dark:text-amber-200',
                lost: 'border-red-500/50 bg-red-50/50 text-red-900 dark:border-red-500/50 dark:bg-red-950/20 dark:text-red-200',
            })"
        >
            <p class="text-sm font-semibold" x-text="connectionHeading">Weak connection detected</p>
            <p class="mt-1 text-sm leading-6" x-text="connectionMessage">Live updates may be delayed. It’s best if one person updates the result until your connection improves.</p>
        </div>
    @endif

    <section class="ui-section" data-result-create-form-section>
        <div class="ui-shell-grid">
            <div class="ui-section-intro gap-2">
                <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-list-numbers size-5 text-neutral-700 dark:text-neutral-200">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M11 6h9" />
                        <path d="M11 12h9" />
                        <path d="M12 18h8" />
                        <path d="M4 16a2 2 0 1 1 4 0c0 .591 -.5 1 -1 1.5l-3 2.5h4" />
                        <path d="M6 10v-6l-2 2" />
                    </svg>
                </span>
                <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                    <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Enter result</h2>
                    <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                        Enter each frame, check the totals, and submit the scorecard when it is correct.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card" data-result-form-shell>
                    <div class="ui-result-form-frame-list" data-result-form-frames>
                        @foreach ($frameRows as $row)
                            @include('livewire.result-form-partials.frame-row', ['row' => $row])
                        @endforeach
                    </div>

                    @php
                        $isMatchDraw = $form->homeScore === $form->awayScore;
                        $homeMatchBadgeClasses = $isMatchDraw
                            ? 'ui-live-score-badge-draw'
                            : ($form->homeScore > $form->awayScore ? 'ui-live-score-badge-win' : 'ui-live-score-badge-loss');
                        $awayMatchBadgeClasses = $isMatchDraw
                            ? 'ui-live-score-badge-draw'
                            : ($form->awayScore > $form->homeScore ? 'ui-live-score-badge-win' : 'ui-live-score-badge-loss');
                    @endphp
                    <div class="ui-result-total-item" data-result-form-band>
                        <p class="ui-result-frame-label">Match total</p>

                        <div class="ui-fixture-team-matchup">
                            <div class="ui-fixture-team-names">
                                <p class="ui-fixture-team-name">{{ $fixture->homeTeam->name }}</p>
                                <p class="ui-fixture-team-name">{{ $fixture->awayTeam->name }}</p>
                            </div>

                            <div class="ui-fixture-item-actions self-center" data-slot="item-actions">
                                <div class="ui-fixture-badge-stack" data-result-score-pill role="group" aria-label="{{ $fixture->homeTeam->name }} {{ $form->homeScore }} to {{ $form->awayScore }} {{ $fixture->awayTeam->name }}">
                                    <span class="ui-fixture-badge {{ $homeMatchBadgeClasses }}">{{ $form->homeScore }}</span>
                                    <span class="ui-fixture-badge {{ $awayMatchBadgeClasses }}">{{ $form->awayScore }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ui-card-footer" data-result-form-actions>
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('fixture.show', $fixture) }}" class="ui-result-button ui-result-button-secondary">
                                Cancel
                            </a>

                            @if (! $isLocked && $canEdit)
                                <button
                                    type="submit"
                                    class="ui-result-button ui-result-button-primary"
                                    wire:loading.attr="disabled"
                                    wire:target="submit"
                                >
                                    Submit result
                                </button>
                            @elseif ($isLocked)
                                <div class="flex items-center text-sm font-medium text-green-700 dark:text-green-400">
                                    Result submitted
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($lastEditedAt)
                        <div class="border-t border-border px-5 py-3" data-result-form-subfooter>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                Last edited{{ $lastUpdatedByName ? ' by '.$lastUpdatedByName : '' }} {{ $lastEditedAt }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if ($errors->any())
        <div class="mx-auto w-full max-w-4xl" data-result-form-errors>
            <x-errors />
        </div>
    @endif
</form>
