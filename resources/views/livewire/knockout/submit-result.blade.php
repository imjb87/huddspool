<div class="w-full" data-knockout-submit-form>
    @if (session('status'))
        <div class="ui-card mb-6" data-knockout-submit-success>
            <div class="ui-card-body">
                <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('status') }}</p>
            </div>
        </div>
    @endif

    <form wire:submit.prevent="submit" class="space-y-6" data-knockout-submit-form-fields>
        <div class="ui-card" data-knockout-submit-form-shell>
            <div class="ui-card-rows" data-knockout-submit-rows>
                <div class="ui-card-row" data-knockout-submit-home-row>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $match->homeParticipant?->display_name ?? 'Home participant' }}
                            </p>
                        </div>

                        <div class="shrink-0">
                            <div class="ui-score-pill ui-score-pill-input">
                                <input type="number"
                                    min="0"
                                    wire:model="homeScore"
                                    class="block h-7 w-full appearance-none border-0 bg-transparent px-0 py-0 text-center text-xs font-extrabold text-gray-700 focus:outline-0 focus:ring-0 dark:text-gray-200 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                    data-knockout-submit-home-score>
                            </div>
                        </div>
                </div>

                @error('homeScore')
                    <div class="px-5 pb-4 -mt-2">
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    </div>
                @enderror

                <div class="ui-card-row" data-knockout-submit-away-row>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $match->awayParticipant?->display_name ?? 'Away participant' }}
                            </p>
                        </div>

                        <div class="shrink-0">
                            <div class="ui-score-pill ui-score-pill-input">
                                <input type="number"
                                    min="0"
                                    wire:model="awayScore"
                                    class="block h-7 w-full appearance-none border-0 bg-transparent px-0 py-0 text-center text-xs font-extrabold text-gray-700 focus:outline-0 focus:ring-0 dark:text-gray-200 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                    data-knockout-submit-away-score>
                            </div>
                        </div>
                </div>

                @error('awayScore')
                    <div class="px-5 pb-4 -mt-2">
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    </div>
                @enderror
            </div>

            <div class="ui-card-footer">
                <div class="flex justify-end gap-x-3">
                    <a href="{{ route('knockout.show', $match->round->knockout) }}"
                        class="ui-button-secondary">
                        Cancel
                    </a>

                    <button type="submit"
                        class="ui-button-primary"
                        data-knockout-submit-button>
                        Submit result
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
