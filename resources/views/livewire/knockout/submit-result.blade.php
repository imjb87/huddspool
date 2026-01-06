<div class="bg-white shadow sm:rounded-lg p-6 space-y-6">
    @if (session('status'))
        <div class="rounded-md bg-green-50 p-4">
            <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
        </div>
    @endif
    <div>
        <p class="text-sm text-gray-500">Enter the final score. First to {{ $match->targetScoreToWin() }} wins
            ({{ $match->bestOfValue() }} frames).</p>
    </div>
    <form wire:submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-x-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    {{ $match->homeParticipant?->display_name ?? 'Home participant' }}
                </label>
                <input type="number" min="0" wire:model="homeScore"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600">
                @error('homeScore')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    {{ $match->awayParticipant?->display_name ?? 'Away participant' }}
                </label>
                <input type="number" min="0" wire:model="awayScore"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600">
                @error('awayScore')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="flex justify-end">
            <button type="submit"
                class="inline-flex items-center rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700">
                Submit result
            </button>
        </div>
    </form>
</div>
