<section class="w-full overflow-hidden border-y border-gray-200 bg-white shadow-md" data-knockout-submit-form>
    <div class="bg-linear-to-b from-gray-50 to-gray-100">
        <div class="mx-auto flex w-full max-w-4xl flex-col gap-2 px-4 py-4 sm:px-6">
            <h3 class="text-sm font-semibold text-gray-900 sm:text-base">Match score</h3>
            <p class="text-sm text-gray-500">
                Enter the final score. First to {{ $match->targetScoreToWin() }} wins ({{ $match->bestOfValue() }} frames).
            </p>
        </div>
    </div>

    <div class="mx-auto w-full max-w-4xl px-4 py-6 sm:px-6">
        @if (session('status'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3" data-knockout-submit-success>
                <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
            </div>
        @endif

        <form wire:submit.prevent="submit" class="space-y-6" data-knockout-submit-form-fields>
            <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_88px_minmax(0,1fr)] lg:items-end">
                <div class="rounded-xl border border-gray-200 bg-gray-50/80 px-4 py-4">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Home participant
                    </label>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $match->homeParticipant?->display_name ?? 'Home participant' }}
                    </p>
                    <input type="number"
                        min="0"
                        wire:model="homeScore"
                        class="mt-4 block w-full rounded-full border-gray-300 bg-white px-4 py-2.5 text-sm shadow-xs focus:border-green-600 focus:ring-green-600"
                        data-knockout-submit-home-score>
                    @error('homeScore')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-center">
                    <span class="inline-flex h-8 min-w-[64px] items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-3 text-xs font-bold uppercase tracking-wide text-white shadow-sm ring-1 ring-black/10">
                        Final
                    </span>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50/80 px-4 py-4">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Away participant
                    </label>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $match->awayParticipant?->display_name ?? 'Away participant' }}
                    </p>
                    <input type="number"
                        min="0"
                        wire:model="awayScore"
                        class="mt-4 block w-full rounded-full border-gray-300 bg-white px-4 py-2.5 text-sm shadow-xs focus:border-green-600 focus:ring-green-600"
                        data-knockout-submit-away-score>
                    @error('awayScore')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-black/10 transition hover:brightness-110 focus-visible:outline-solid focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700"
                    data-knockout-submit-button>
                    Submit result
                </button>
            </div>
        </form>
    </div>
</section>
