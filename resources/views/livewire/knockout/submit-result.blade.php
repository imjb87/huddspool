<div class="w-full" data-knockout-submit-form>
    @if (session('status'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3" data-knockout-submit-success>
            <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
        </div>
    @endif

    <form wire:submit.prevent="submit" class="space-y-6" data-knockout-submit-form-fields>
        <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
            <div>
                <p class="text-sm font-semibold text-gray-900">
                    {{ $match->homeParticipant?->display_name ?? 'Home participant' }}
                </p>
                <input type="number"
                    min="0"
                    wire:model="homeScore"
                    class="mt-3 block w-full rounded-lg border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20"
                    data-knockout-submit-home-score>
                @error('homeScore')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <p class="text-sm font-semibold text-gray-900">
                    {{ $match->awayParticipant?->display_name ?? 'Away participant' }}
                </p>
                <input type="number"
                    min="0"
                    wire:model="awayScore"
                    class="mt-3 block w-full rounded-lg border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20"
                    data-knockout-submit-away-score>
                @error('awayScore')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end border-t border-gray-200 pt-6">
            <button type="submit"
                class="inline-flex items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-black/10 transition hover:brightness-110 focus-visible:outline-solid focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700"
                data-knockout-submit-button>
                Submit result
            </button>
        </div>
    </form>
</div>
