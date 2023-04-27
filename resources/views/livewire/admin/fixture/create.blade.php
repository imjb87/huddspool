<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">{{ $section->name }} Fixtures</h1>
            <p class="mt-2 text-sm text-gray-700">Please review the generated fixtures below and confirm that they are
                correct.</p>
            @if ($errors->any())
              <x-errors />
            @endif
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 flex gap-x-3">
            <a href="{{ route('admin.sections.show', $section) }}"
                class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Cancel</a>
            <button type="button"
                class="rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 inline-flex items-center gap-x-2"
                wire:click="save"
                wire:loading.attr="disabled"
                wire:target="save"
                >
                <span wire:loading wire:target="save" class="animate-spin">
                    <svg class="h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M12 6v6l4 2"></path>
                    </svg>
                </span>
                Save
            </button>
        </div>
    </div>
    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <table class="min-w-full shadow rounded-md overflow-hidden table-fixed">
                    <thead class="bg-white">
                        <tr>
                            <th scope="col"
                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-3">Date</th>
                            <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Home
                                team</th>
                            <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900"></th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Away
                                team</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Venue
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach ($schedule as $week => $fixtures)
                            <tr class="border-t border-gray-200">
                                <th colspan="5" scope="colgroup"
                                    class="bg-gray-50 py-2 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-3">
                                    Week {{ $week }}</th>
                            </tr>

                            @foreach ($fixtures as $fixture)
                                <tr class="border-t border-gray-300">
                                    <td
                                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-3">
                                        {{ date('d/m/Y', strtotime($fixture['fixture_date'])) }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-right">
                                        {{ $fixture['home_team_name'] }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-center">vs</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $fixture['away_team_name'] }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $fixture['venue_name'] }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
