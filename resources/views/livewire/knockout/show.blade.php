<div data-knockout-show-page x-on:knockout-round-changed.window="window.scrollTo(0, 0)">
    <div class="mx-auto max-w-7xl pt-6 lg:pt-7">
        <div class="mx-auto flex w-full max-w-4xl items-center justify-between gap-3 px-4 pb-4 sm:px-6" data-knockout-shared-header>
            <h1 class="text-lg font-semibold text-gray-900">{{ $knockout->name }}</h1>
            <p class="text-sm text-gray-500">{{ $knockout->season->name }}</p>
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
            <section class="w-full overflow-hidden border-y border-gray-200 bg-white shadow-md" data-knockout-round-shell>
                <div class="border-b border-gray-300 bg-linear-to-b from-gray-50 to-gray-100">
                    <div class="mx-auto flex w-full max-w-4xl items-center justify-between gap-3 px-4 py-2 sm:px-6" data-knockout-round-header>
                        <div class="text-sm font-semibold text-gray-900">
                            {{ $round->scheduled_for?->format('j F Y') ?? 'Date TBC' }}
                        </div>
                        <div class="text-sm font-semibold text-gray-900">
                            Best of {{ $round->bestOfValue() }} frames
                        </div>
                    </div>
                </div>

                <div class="bg-white" data-knockout-round-body>
                    @if ($round->matches->isNotEmpty())
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
                    @else
                        <div class="px-4 py-10 text-center sm:px-6" data-knockout-empty-state>
                            <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 px-6 py-8">
                                <h3 class="text-sm font-semibold text-gray-900">No matches scheduled for this round yet.</h3>
                                <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500">
                                    Match pairings and dates will appear here once the bracket is ready.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @else
            <div class="px-4 py-10 text-center sm:px-6" data-knockout-empty-state>
                <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 bg-white px-6 py-8 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-900">No rounds have been published yet.</h3>
                    <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500">
                        The bracket will appear here as soon as round information is published.
                    </p>
                </div>
            </div>
        @endif
    </div>

    @if ($this->currentRound)
        <div class="mx-auto w-full max-w-4xl px-4 pt-5 pb-4 sm:px-6 lg:pt-5 lg:pb-6" data-knockout-round-controls>
            <div class="grid w-full grid-cols-[1fr_auto_1fr] items-center">
                <div class="flex justify-start">
                    <button wire:click="previousRound"
                        wire:loading.attr="disabled"
                        class="inline-flex w-24 cursor-pointer items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-3 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                        aria-label="Previous round"
                        @disabled(! $this->hasPreviousRound)>
                        Previous
                    </button>
                </div>

                <div class="flex items-center justify-center px-4">
                    <span class="text-sm font-semibold text-gray-900" data-knockout-current-round-label>
                        {{ $this->currentRound->name }}
                    </span>
                </div>

                <div class="flex justify-end">
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
    @endif
</div>
