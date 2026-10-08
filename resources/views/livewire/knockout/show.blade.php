<div data-knockout-show-page x-on:knockout-round-changed.window="window.scrollTo(0, 0)">
    <div wire:loading.block wire:target="previousRound, nextRound" data-knockout-round-skeleton>
        @include('knockouts.partials.round-skeleton')
    </div>

    <div wire:loading.remove wire:target="previousRound, nextRound" data-knockout-round-panel>
        @if ($this->currentRound)
            <section class="ui-section" data-knockout-round-shell>
                <div class="ui-shell-grid">
                    <div>
                        <div class="ui-section-intro gap-2">
                            <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-tournament size-5 text-neutral-700 dark:text-neutral-200">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M2 4a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M18 10a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M2 12a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M2 20a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M6 12h3a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-3" />
                                    <path d="M6 4h7a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-2" />
                                    <path d="M14 10h4" />
                                </svg>
                            </span>

                            <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">{{ $this->currentRound->name }}</h2>
                                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                                    @if ($this->knockout->type === \App\KnockoutType::Singles)
                                        To be played by {{ $this->currentRound->scheduled_for?->format('j F Y') ?? 'date TBC' }}
                                    @else
                                        {{ $this->currentRound->scheduled_for?->format('j F Y \\a\\t H:i') ?? 'Date TBC' }}
                                    @endif
                                    <span class="text-neutral-300 dark:text-neutral-600">&middot;</span>
                                    Best of {{ $this->currentRound->bestOfValue() }} frames
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="ui-card ui-knockout-round-card">
                            @if ($this->currentRoundRows->isNotEmpty())
                                <div class="ui-knockout-match-group" data-knockout-round-body data-slot="item-group">
                                    @foreach ($this->currentRoundRows as $matchRow)
                                        @include('knockouts.partials.match-row', ['matchRow' => $matchRow])
                                    @endforeach
                                </div>
                            @else
                                <x-ui-empty-state
                                    title="No matches scheduled for this round yet."
                                    description="Match pairings will appear here once this round is scheduled."
                                    data-knockout-empty-state
                                />
                            @endif

                            <div class="border-t border-border px-3 py-4 sm:px-5" data-knockout-round-controls>
                                <nav class="ui-pagination" aria-label="Knockout round pagination">
                                    <button wire:click="previousRound"
                                        wire:loading.attr="disabled"
                                        class="ui-pagination-link"
                                        aria-label="Previous round"
                                        type="button"
                                        @disabled(! $this->hasPreviousRound)>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left size-4" aria-hidden="true">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M15 6l-6 6l6 6" />
                                        </svg>
                                        <span class="hidden sm:inline">Previous</span>
                                    </button>

                                    <span class="ui-pagination-current" aria-live="polite" data-knockout-current-round-label>
                                        {{ $this->currentRound->name }}
                                    </span>

                                    <button wire:click="nextRound"
                                        wire:loading.attr="disabled"
                                        class="ui-pagination-link"
                                        aria-label="Next round"
                                        type="button"
                                        @disabled(! $this->hasNextRound)>
                                        <span class="hidden sm:inline">Next</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right size-4" aria-hidden="true">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M9 6l6 6l-6 6" />
                                        </svg>
                                    </button>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @else
            <div class="ui-section" data-knockout-empty-state>
                <div class="ui-shell-grid">
                    <div>
                        <div class="ui-section-intro gap-2">
                            <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-tournament size-5 text-neutral-700 dark:text-neutral-200">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M2 4a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M18 10a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M2 12a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M2 20a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M6 12h3a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-3" />
                                    <path d="M6 4h7a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-2" />
                                    <path d="M14 10h4" />
                                </svg>
                            </span>

                            <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Rounds</h2>
                                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                                    Open a published round to see its match pairings and results.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="ui-card">
                            <x-ui-empty-state
                                title="No rounds have been published yet."
                                description="Rounds will appear here when the competition bracket is published."
                                data-knockout-empty-state
                            />
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
