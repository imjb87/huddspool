<div data-knockout-show-page x-on:knockout-round-changed.window="window.scrollTo(0, 0)">
    <div class="mx-auto max-w-7xl pt-6 lg:pt-7">
        <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pb-4 sm:px-6" data-knockout-shared-header>
            <div class="min-w-0">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $knockout->season->name }}</p>
                <h1 class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $knockout->name }}</h1>
            </div>
        </div>
    </div>

    <div wire:loading.block wire:target="previousRound, nextRound" data-knockout-round-skeleton>
        @include('knockouts.partials.round-skeleton')
    </div>

    <div wire:loading.remove wire:target="previousRound, nextRound" data-knockout-round-panel>
        @if ($this->currentRound)
            @php
                $round = $this->currentRound;
            @endphp
            <section class="mx-auto mt-6 w-full max-w-4xl px-4 sm:px-6 lg:px-6" data-knockout-round-shell>
                <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                    <div class="space-y-2">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $round->name }}</h2>
                        <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                            {{ $round->scheduled_for?->format('j F Y') ?? 'Date TBC' }}
                            <span class="text-gray-300 dark:text-zinc-600">/</span>
                            Best of {{ $round->bestOfValue() }} frames
                        </p>
                    </div>

                    <div class="lg:col-span-2">
                        @if ($round->matches->isNotEmpty())
                            <div class="divide-y divide-gray-200 dark:divide-zinc-800/80" data-knockout-round-body>
                                @foreach ($round->matches as $match)
                                    @php
                                        $matchLabel = isset($matchNumbers[$match->id]) ? 'Match '.$matchNumbers[$match->id] : null;
                                        $homeLabel = $this->slotLabel($match, 'home');
                                        $awayLabel = $this->slotLabel($match, 'away');
                                        $hasBye = ($match->home_participant_id && ! $match->away_participant_id)
                                            || ($match->away_participant_id && ! $match->home_participant_id);
                                    @endphp

                                    @include('knockouts.partials.match-row', [
                                        'knockout' => $knockout,
                                        'match' => $match,
                                        'matchLabel' => $matchLabel,
                                        'homeLabel' => $homeLabel,
                                        'awayLabel' => $awayLabel,
                                        'hasBye' => $hasBye,
                                    ])
                                @endforeach
                            </div>
                        @else
                            <div class="px-4 py-10 text-center sm:px-6" data-knockout-empty-state>
                                <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 px-6 py-8 dark:border-zinc-700 dark:bg-zinc-800/75">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">No matches scheduled for this round yet.</h3>
                                    <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                                        Match pairings and dates will appear here once the bracket is ready.
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div class="pt-5 pb-4 lg:pt-5 lg:pb-6" data-knockout-round-controls>
                            <div class="flex items-center justify-between gap-4">
                                <button wire:click="previousRound"
                                    wire:loading.attr="disabled"
                                    class="inline-flex w-24 cursor-pointer items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-3 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                                    aria-label="Previous round"
                                    @disabled(! $this->hasPreviousRound)>
                                    Previous
                                </button>

                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100" data-knockout-current-round-label>
                                    {{ $this->currentRound->name }}
                                </span>

                                <button wire:click="nextRound"
                                    wire:loading.attr="disabled"
                                    class="inline-flex w-24 cursor-pointer items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-3 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
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
            <div class="mx-auto mt-6 w-full max-w-4xl px-4 sm:px-6 lg:px-6" data-knockout-empty-state>
                <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                    <div class="space-y-2">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Rounds</h2>
                        <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                            Published rounds and ties will appear here once the bracket is ready.
                        </p>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="px-4 py-10 text-center sm:px-6">
                            <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 bg-white px-6 py-8 shadow-sm dark:border-zinc-700 dark:bg-zinc-800/75 dark:shadow-none">
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
