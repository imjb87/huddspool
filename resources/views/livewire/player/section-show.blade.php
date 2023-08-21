<section>
    <div class="bg-white shadow rounded-lg flex flex-col h-full overflow-hidden">
        <div class="px-4 py-4 sm:px-6 bg-green-700">
            <h2 class="text-sm font-medium leading-6 text-white">{{ $section->name }}</h2>
        </div>
        <div class="border-t border-gray-200 h-full flex flex-col">
            <div class="min-w-full overflow-hidden">
                <div class="bg-gray-50 flex">
                    <div scope="col" class="px-4 sm:px-6 py-2 text-sm font-semibold text-gray-900 w-[10%]">#
                    </div>
                    <div scope="col" class="px-4 sm:px-6 py-2 text-sm font-semibold text-gray-900 w-[54%]">Name
                    </div>
                    <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-[12%] text-center">Pl
                    </div>
                    <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-[12%] text-center">W
                    </div>
                    <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-[12%] text-center">L
                    </div>
                </div>
                <div class="bg-white">
                    <!-- if there are players, otherwise show empty state -->
                    @if (count($players) == 0 )
                        <div class="text-center m-4 p-4 rounded-lg border-2 border-dashed border-gray-300">
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">No frames</h3>
                            <p class="mt-1 text-sm text-gray-500 max-w-prose mx-auto">There have been no frames played in this section yet. Please check back here again soon.</p>
                        </div>
                    @else 
                        @foreach ($players as $player)
                            <a class="flex w-full border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50" href="{{ route('player.show', $player->id) }}">
                                <div class="whitespace-nowrap px-4 sm:px-6 py-2 text-sm text-gray-900 w-[10%] font-semibold">
                                    {{ $loop->iteration + ( ($page - 1) * 10 ) }}.</div>
                                <div class="whitespace-nowrap px-4 sm:px-6 py-2 text-sm text-gray-900 w-[54%]">
                                    {{$player->name}}
                                </div>
                                <div class="py-2 text-sm text-gray-900 w-[12%] text-center font-semibold">
                                    {{$player->total_frames}}</div>
                                <div class="py-2 text-sm text-gray-900 w-[12%] text-center font-semibold">
                                    {{$player->total_score}}</div>
                                <div class="py-2 text-sm text-gray-900 w-[12%] text-center font-semibold">
                                    {{$player->total_against}}</div>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <!-- pagination -->
        <div class="mt-auto px-4 py-4 sm:px-6 border-t border-gray-200">
            <div class="flex">
                <button wire:click="previousPage" wire:loading.attr="disabled"
                    class="inline-flex items-center px-2 py-1 border border-green-700/20 text-sm font-medium rounded-md text-white bg-green-700 hover:bg-green-700 disabled:opacity-50"
                    aria-label="Previous"
                    {{ $page == 1 ? 'disabled' : '' }}
                    >
                    &laquo; Previous 
                </button>

                <button wire:click="nextPage" wire:loading.attr="disabled"
                    class="ml-auto inline-flex items-center px-2 py-1 border border-green-700/20 text-sm font-medium rounded-md text-white bg-green-700 hover:bg-green-700 disabled:opacity-50"
                    aria-label="Next"
                    {{ $page >= $totalPages ? 'disabled' : '' }}
                    >
                    Next &raquo;
                </button>

            </div>
        </div>
    </div>
</section>
