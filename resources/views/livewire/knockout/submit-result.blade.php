<div class="w-full" data-knockout-submit-form>
    @if (session('status'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 dark:border-green-800/80 dark:bg-green-950/40" data-knockout-submit-success>
            <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('status') }}</p>
        </div>
    @endif

    <form wire:submit.prevent="submit" class="space-y-6" data-knockout-submit-form-fields>
        <div class="space-y-4" data-knockout-submit-form-shell>
            <div class="divide-y divide-gray-200 dark:divide-zinc-800/80" data-knockout-submit-rows>
                <div class="py-4">
                    <div class="flex items-center justify-between gap-4" data-knockout-submit-home-row>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $match->homeParticipant?->display_name ?? 'Home participant' }}
                            </p>
                        </div>

                        <div class="shrink-0">
                            <div class="inline-flex h-7 w-14 overflow-hidden rounded-full bg-gray-100 text-center text-xs font-extrabold text-gray-700 ring-1 ring-gray-200 dark:bg-zinc-700 dark:text-gray-200 dark:ring-zinc-700">
                            <input type="number"
                                min="0"
                                wire:model="homeScore"
                                class="block h-7 w-full appearance-none border-0 bg-transparent px-0 py-0 text-center text-xs font-extrabold text-gray-700 focus:outline-0 focus:ring-0 dark:text-gray-200 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                data-knockout-submit-home-score>
                            </div>
                        </div>
                    </div>

                    @error('homeScore')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="py-4">
                    <div class="flex items-center justify-between gap-4" data-knockout-submit-away-row>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $match->awayParticipant?->display_name ?? 'Away participant' }}
                            </p>
                        </div>

                        <div class="shrink-0">
                            <div class="inline-flex h-7 w-14 overflow-hidden rounded-full bg-gray-100 text-center text-xs font-extrabold text-gray-700 ring-1 ring-gray-200 dark:bg-zinc-700 dark:text-gray-200 dark:ring-zinc-700">
                            <input type="number"
                                min="0"
                                wire:model="awayScore"
                                class="block h-7 w-full appearance-none border-0 bg-transparent px-0 py-0 text-center text-xs font-extrabold text-gray-700 focus:outline-0 focus:ring-0 dark:text-gray-200 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                data-knockout-submit-away-score>
                            </div>
                        </div>
                    </div>

                    @error('awayScore')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-x-3 pt-2">
            <a href="{{ route('knockout.show', $match->round->knockout) }}"
                class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-900 shadow-xs ring-1 ring-inset ring-slate-300 transition hover:bg-slate-50 dark:bg-zinc-900 dark:text-gray-100 dark:ring-zinc-700 dark:hover:bg-zinc-800">
                Cancel
            </a>

            <button type="submit"
                class="inline-flex items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-4 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 transition hover:brightness-110 focus-visible:outline-solid focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700"
                data-knockout-submit-button>
                Submit result
            </button>
        </div>
    </form>
</div>
