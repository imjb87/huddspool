<section>
    <div class="bg-white shadow rounded-lg flex flex-col h-full overflow-hidden">
        <div class="px-4 py-4 sm:px-6 bg-green-700">
            <h2 class="text-sm font-medium leading-6 text-white">{{ $section->name }}</h2>
        </div>
        <div class="border-t border-gray-200 h-full flex flex-col">
            <div class="min-w-full overflow-hidden">
                <div class="bg-white border-b border-gray-300">
                    <div scope="col" class="px-2 py-2 text-center text-sm font-semibold text-gray-900 w-full">Week {{ $week }}
                    </div>
                </div>
                <div class="bg-gray-50 flex">
                    <div scope="col" class="px-2 py-2 text-right text-sm font-semibold text-gray-900 w-5/12">Home
                    </div>
                    <div scope="col" class="px-2 py-2 text-center text-sm font-semibold text-gray-900 w-1/12"></div>
                    <div scope="col" class="px-2 py-2 text-center text-sm font-semibold text-gray-900 w-1/12">
                    </div>
                    <div scope="col" class="px-2 py-2 text-left text-sm font-semibold text-gray-900 w-5/12">Away
                    </div>
                </div>
                <div class="bg-white border-b border-gray-300">
                    @foreach ($fixtures as $fixture)
                        <a class="flex w-full border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50"
                            href="{{ $fixture->result ? route('result.show', $fixture->result) : route('fixture.show', $fixture) }}">
                            <div
                                class="whitespace-nowrap px-2 py-4 text-sm text-gray-900 text-right w-5/12 {{ $fixture->homeTeam->shortname ? 'hidden md:block' : '' }}">
                                {{ $fixture->homeTeam->name }}</div>
                            @if ($fixture->homeTeam->shortname)
                                <div
                                    class="whitespace-nowrap px-2 py-4 text-sm text-gray-900 text-right w-5/12 md:hidden">
                                    {{ $fixture->homeTeam->shortname }}</div>
                            @endif
                            @if ($fixture->result)
                                <div
                                    class="whitespace-nowrap px-1 py-3 text-sm text-gray-500 text-right font-semibold w-2/12 flex">
                                    <div class="inline-block bg-green-700 text-white text-center mx-auto text-xs leading-7 min-w-[44px] font-extrabold">
                                        {{ $fixture->result->home_score ?? '' }} -
                                        {{ $fixture->result->away_score ?? '' }}</div>
                                </div>
                            @else
                                <div
                                    class="whitespace-nowrap px-2 py-4 text-sm text-gray-500 text-center font-semibold w-2/12">
                                    {{ $fixture->fixture_date->format('d/m') }}
                                </div>
                            @endif

                            <div
                                class="whitespace-nowrap px-2 py-4 text-sm text-gray-900 text-left w-5/12 {{ $fixture->awayTeam->shortname ? 'hidden md:block' : '' }}">
                                {{ $fixture->awayTeam->name }}</div>
                            @if ($fixture->awayTeam->shortname)
                                <div
                                    class="whitespace-nowrap px-2 py-4 text-sm text-gray-900 text-left w-5/12 md:hidden">
                                    {{ $fixture->awayTeam->shortname }}</div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="mt-auto px-4 py-4 sm:px-6 flex justify-between">
                <button wire:click="previousWeek" wire:loading.attr="disabled"
                    class="inline-flex items-center px-2 py-1 border border-green-700/20 text-sm font-medium rounded-md text-white bg-green-700 hover:bg-green-700 disabled:opacity-50"
                    aria-label="Previous"
                    {{ $week == 1 ? 'disabled' : '' }}
                    >
                    &laquo; Previous 
                </button>

                <button wire:click="nextWeek" wire:loading.attr="disabled"
                    class="ml-auto inline-flex items-center px-2 py-1 border border-green-700/20 text-sm font-medium rounded-md text-white bg-green-700 hover:bg-green-700 disabled:opacity-50"
                    aria-label="Next"
                    {{ $week >= 18 ? 'disabled' : '' }}
                    >
                    Next &raquo;
                </button>                
            </div>
        </div>
    </div>
</section>
