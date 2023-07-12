<section>
    <div class="bg-white shadow rounded-lg flex flex-col h-full">
        <div class="px-4 py-5 sm:px-6">
            <h2 class="text-lg font-medium leading-6 text-gray-900">{{ $section->name }}</h2>
        </div>
        <div class="border-t border-gray-200 h-full flex flex-col">
            <div class="min-w-full overflow-hidden">
                <div class="bg-gray-50 flex">
                    <div scope="col" class="px-2 py-2 text-right text-sm font-semibold text-gray-900 w-5/12">Home
                    </div>
                    <div scope="col" class="px-2 py-2 text-center text-sm font-semibold text-gray-900 w-1/12"></div>
                    <div scope="col" class="px-2 py-2 text-center text-sm font-semibold text-gray-900 w-1/12">
                    </div>
                    <div scope="col" class="px-2 py-2 text-left text-sm font-semibold text-gray-900 w-5/12">Away
                    </div>
                </div>
                <div class="bg-white">
                    @foreach ($fixtures as $fixture)
                        <a class="flex w-full border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50" href="{{ route('fixture.show', $fixture) }}">
                            <div class="whitespace-nowrap px-2 py-4 text-sm text-gray-500 text-right w-5/12">
                                {{ $fixture->homeTeam->name }}</div>
                            @if ($fixture->result)
                                <div class="whitespace-nowrap px-1 py-4 text-sm text-gray-500 text-right font-semibold w-1/12">
                                    <span
                                        class="inline-block bg-green-700 text-white rounded-md w-6 text-center">{{ $fixture->result->home_score ?? '' }}</span>
                                </div>
                                <div class="whitespace-nowrap px-1 py-4 text-sm text-gray-500 font-semibold w-1/12">
                                    <span
                                        class="inline-block bg-green-700 text-white rounded-md w-6 text-center">{{ $fixture->result->away_score ?? '' }}</span>
                                </div>
                            @else
                                <div
                                    class="whitespace-nowrap px-2 py-4 text-sm text-gray-500 text-center font-semibold w-2/12">
                                    {{ $fixture->fixture_date->format('d/m') }}
                                </div>
                            @endif
                            <div class="whitespace-nowrap px-2 py-4 text-sm text-gray-500 text-left w-5/12">
                                {{ $fixture->awayTeam->name }}</div>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="mt-auto px-4 py-4 sm:px-6">
                {{ $fixtures->links() }}
            </div>
        </div>
    </div>
</section>
