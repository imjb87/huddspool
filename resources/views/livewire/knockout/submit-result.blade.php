@php
    $isDraw = $homeScore === $awayScore;
    $homeScoreBadgeClasses = $isDraw
        ? 'ui-fixture-badge-neutral'
        : ($homeScore > $awayScore ? 'ui-live-score-badge-win' : 'ui-live-score-badge-loss');
    $awayScoreBadgeClasses = $isDraw
        ? 'ui-fixture-badge-neutral'
        : ($awayScore > $homeScore ? 'ui-live-score-badge-win' : 'ui-live-score-badge-loss');
@endphp

<div class="w-full" data-knockout-submit-form>
    @if (session('status'))
        <div class="ui-card mb-4" data-knockout-submit-success>
            <div class="ui-card-body">
                <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('status') }}</p>
            </div>
        </div>
    @endif

    <form wire:submit.prevent="submit" class="space-y-4" data-knockout-submit-form-fields>
        <div class="ui-card" data-knockout-submit-form-shell>
            <div class="ui-result-form-frame-list" data-knockout-submit-rows>
                <div class="ui-result-form-frame-item" data-knockout-submit-matchup>
                    <p class="ui-result-form-frame-label">Final score</p>

                    <div class="ui-fixture-team-matchup">
                        <div class="ui-fixture-team-names">
                            <p class="ui-fixture-team-name">{{ $match->homeParticipant?->display_name ?? 'Home participant' }}</p>
                            <p class="ui-fixture-team-name">{{ $match->awayParticipant?->display_name ?? 'Away participant' }}</p>
                        </div>

                        <div class="ui-fixture-item-actions self-center" data-slot="item-actions">
                            <div class="ui-fixture-badge-stack" data-knockout-submit-score-pill role="group" aria-label="{{ $match->homeParticipant?->display_name ?? 'Home participant' }} {{ $homeScore }} to {{ $awayScore }} {{ $match->awayParticipant?->display_name ?? 'Away participant' }}">
                                <div class="ui-result-form-score-field ui-fixture-badge {{ $homeScoreBadgeClasses }} transition-shadow duration-150 focus-within:ring-2 focus-within:ring-foreground/20" data-knockout-submit-home-score-field>
                                    <input
                                        type="number"
                                        min="0"
                                        max="{{ $match->targetScoreToWin() }}"
                                        inputmode="numeric"
                                        wire:model.live="homeScore"
                                        class="block h-8 w-full appearance-none border-0 bg-transparent px-0 py-0 text-center text-xs font-semibold text-inherit outline-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                        data-knockout-submit-home-score
                                        aria-label="{{ $match->homeParticipant?->display_name ?? 'Home participant' }} score"
                                    >
                                </div>

                                <div class="ui-result-form-score-field ui-fixture-badge {{ $awayScoreBadgeClasses }} transition-shadow duration-150 focus-within:ring-2 focus-within:ring-foreground/20" data-knockout-submit-away-score-field>
                                    <input
                                        type="number"
                                        min="0"
                                        max="{{ $match->targetScoreToWin() }}"
                                        inputmode="numeric"
                                        wire:model.live="awayScore"
                                        class="block h-8 w-full appearance-none border-0 bg-transparent px-0 py-0 text-center text-xs font-semibold text-inherit outline-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                        data-knockout-submit-away-score
                                        aria-label="{{ $match->awayParticipant?->display_name ?? 'Away participant' }} score"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ui-card-footer" data-knockout-submit-actions>
                <div class="flex justify-end gap-3">
                    <a href="{{ route('knockout.show', $match->round->knockout) }}" class="ui-result-button ui-result-button-secondary">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="ui-result-button ui-result-button-primary"
                        data-knockout-submit-button
                        wire:loading.attr="disabled"
                        wire:target="submit"
                    >
                        Submit result
                    </button>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div data-knockout-submit-errors>
                <x-errors />
            </div>
        @endif
    </form>
</div>
