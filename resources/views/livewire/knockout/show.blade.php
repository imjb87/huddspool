<div data-knockout-show-page x-on:knockout-round-changed.window="window.scrollTo(0, 0)">
    <div wire:loading.block wire:target="previousRound, nextRound" data-knockout-round-skeleton>
        @include('knockouts.partials.round-skeleton')
    </div>

    <div wire:loading.remove wire:target="previousRound, nextRound" data-knockout-round-panel>
        @if ($this->currentRound)
            <section class="ui-section" data-knockout-round-shell>
                <div class="ui-shell-grid">
                    <div class="ui-section-intro">
                        <div class="ui-section-intro-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 4.5v15m0-15a2.25 2.25 0 0 1 2.25 2.25v1.5A2.25 2.25 0 0 1 6.75 10.5m0-6a2.25 2.25 0 0 0-2.25 2.25v1.5A2.25 2.25 0 0 0 6.75 10.5m0 0v3m0 6a2.25 2.25 0 0 0 2.25-2.25v-1.5A2.25 2.25 0 0 0 6.75 13.5m0 6a2.25 2.25 0 0 1-2.25-2.25v-1.5A2.25 2.25 0 0 1 6.75 13.5m10.5-9v15m0-15A2.25 2.25 0 0 1 19.5 6.75v1.5a2.25 2.25 0 0 1-2.25 2.25m0-6A2.25 2.25 0 0 0 15 6.75v1.5a2.25 2.25 0 0 0 2.25 2.25m0 0v3m0 6a2.25 2.25 0 0 0 2.25-2.25v-1.5a2.25 2.25 0 0 0-2.25-2.25m0 6A2.25 2.25 0 0 1 15 17.25v-1.5a2.25 2.25 0 0 1 2.25-2.25" />
                            </svg>
                        </div>
                        <div class="ui-section-intro-copy">
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $this->currentRound->name }}</h2>
                            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                {{ $this->currentRound->scheduled_for?->format('j F Y') ?? 'Date TBC' }}
                                <span class="text-gray-300 dark:text-neutral-600">/</span>
                                Best of {{ $this->currentRound->bestOfValue() }} frames
                            </p>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        @if ($this->currentRoundRows->isNotEmpty())
                            <div class="ui-card">
                                <div class="ui-card-rows" data-knockout-round-body>
                                    @foreach ($this->currentRoundRows as $matchRow)
                                        @include('knockouts.partials.match-row', ['matchRow' => $matchRow])
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="ui-card" data-knockout-empty-state>
                                <div class="ui-card-body py-10 text-center">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">No matches scheduled for this round yet.</h3>
                                    <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                                        Match pairings and dates will appear here once the bracket is ready.
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div class="pt-4" data-knockout-round-controls>
                            <div class="flex items-center justify-between gap-4">
                                <button wire:click="previousRound"
                                    wire:loading.attr="disabled"
                                    class="ui-button-primary min-w-24 disabled:translate-y-0 disabled:opacity-50"
                                    aria-label="Previous round"
                                    @disabled(! $this->hasPreviousRound)>
                                    Previous
                                </button>

                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100" data-knockout-current-round-label>
                                    {{ $this->currentRound->name }}
                                </span>

                                <button wire:click="nextRound"
                                    wire:loading.attr="disabled"
                                    class="ui-button-primary min-w-24 disabled:translate-y-0 disabled:opacity-50"
                                    aria-label="Next round"
                                    @disabled(! $this->hasNextRound)>
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @else
            <div class="ui-section" data-knockout-empty-state>
                <div class="ui-shell-grid">
                    <div class="ui-section-intro">
                        <div class="ui-section-intro-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 4.5v15m0-15a2.25 2.25 0 0 1 2.25 2.25v1.5A2.25 2.25 0 0 1 6.75 10.5m0-6a2.25 2.25 0 0 0-2.25 2.25v1.5A2.25 2.25 0 0 0 6.75 10.5m0 0v3m0 6a2.25 2.25 0 0 0 2.25-2.25v-1.5A2.25 2.25 0 0 0 6.75 13.5m0 6a2.25 2.25 0 0 1-2.25-2.25v-1.5A2.25 2.25 0 0 1 6.75 13.5m10.5-9v15m0-15A2.25 2.25 0 0 1 19.5 6.75v1.5a2.25 2.25 0 0 1-2.25 2.25m0-6A2.25 2.25 0 0 0 15 6.75v1.5a2.25 2.25 0 0 0 2.25 2.25m0 0v3m0 6a2.25 2.25 0 0 0 2.25-2.25v-1.5a2.25 2.25 0 0 0-2.25-2.25m0 6A2.25 2.25 0 0 1 15 17.25v-1.5a2.25 2.25 0 0 1 2.25-2.25" />
                            </svg>
                        </div>
                        <div class="ui-section-intro-copy">
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Rounds</h2>
                            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                Published rounds and ties will appear here once the bracket is ready.
                            </p>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="ui-card">
                            <div class="ui-card-body py-10 text-center">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">No rounds have been published yet.</h3>
                                <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                                    The bracket will appear here as soon as round information is published.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
