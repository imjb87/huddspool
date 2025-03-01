<section>
    <div class="bg-white shadow rounded-lg flex flex-col h-full overflow-hidden">
        <div class="px-4 py-4 sm:px-6 bg-green-700">
            <h2 class="text-sm font-medium leading-6 text-white">Latest results</h2>
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
                <div class="bg-white border-b border-gray-300">
                    @foreach ($results as $result)
                        <a class="flex w-full border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50"
                            href="{{ route('result.show', $result) }}">
                            <div
                                class="whitespace-nowrap px-2 py-4 text-sm text-gray-900 text-right w-5/12 {{ $result->home_team_name ? 'hidden md:block' : '' }}">
                                {{ $result->home_team_name }}</div>
                            @if ($result->home_team_name)
                                <div
                                    class="whitespace-nowrap px-2 py-4 text-sm text-gray-900 text-right w-5/12 md:hidden">
                                    {{ $result->home_team_name }}</div>
                            @endif
                            @if ($result)
                                <div
                                    class="whitespace-nowrap px-1 py-3 text-sm text-gray-500 text-right font-semibold w-2/12 flex">
                                    <div class="inline-block bg-green-700 text-white text-center mx-auto text-xs leading-7 min-w-[44px] font-extrabold">
                                        {{ $result->home_score ?? '' }} -
                                        {{ $result->away_score ?? '' }}</div>
                                </div>
                            @endif

                            <div
                                class="whitespace-nowrap px-2 py-4 text-sm text-gray-900 text-left w-5/12 {{ $result->away_team_name ? 'hidden md:block' : '' }}">
                                {{ $result->away_team_name }}</div>
                            @if ($result->away_team_name)
                                <div
                                    class="whitespace-nowrap px-2 py-4 text-sm text-gray-900 text-left w-5/12 md:hidden">
                                    {{ $result->away_team_name }}</div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
            @if ($results->hasPages())
                <div class="mt-auto px-4 py-4 sm:px-6">
                    {{ $results->links() }}
                </div>
            @endif
        </div>
    </div>
</section>
