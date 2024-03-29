<x-admin-layout>
    <div class="grid grid-cols-4 gap-6">
        <div class="col-span-4">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Current Season</h3>
            <dl
                class="mt-5 grid grid-cols-1 divide-y divide-gray-200 overflow-hidden rounded-lg bg-white shadow md:grid-cols-3 md:divide-x md:divide-y-0">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-base font-normal text-gray-900">Active Players</dt>
                    <dd class="mt-1 flex items-baseline justify-between md:block lg:flex">
                        <div class="flex items-baseline text-2xl font-semibold text-green-700">
                            {{ $stats->total_players }}
                        </div>
                    </dd>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-base font-normal text-gray-900">Matches Played</dt>
                    <dd class="mt-1 flex items-baseline justify-between md:block lg:flex">
                        <div class="flex items-baseline text-2xl font-semibold text-green-700">
                            {{ $stats->total_results }}
                        </div>
                    </dd>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-base font-normal text-gray-900">Frames Played</dt>
                    <dd class="mt-1 flex items-baseline justify-between md:block lg:flex">
                        <div class="flex items-baseline text-2xl font-semibold text-green-700">
                            {{ $stats->total_frames }}
                        </div>
                    </dd>
                </div>
            </dl>
        </div>
        <div class="col-span-4 md:col-span-2">
            <livewire:admin.outstanding-fixtures />
        </div>
        <div class="col-span-4 md:col-span-2">
            <livewire:admin.latest-results />
        </div>
    </div>
</x-admin-layout>
