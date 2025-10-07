<section>
    <div class="bg-white shadow sm:rounded-lg flex flex-col h-full overflow-hidden -mx-4 sm:mx-0">
        <div class="px-4 py-4 sm:px-6 bg-green-700">
            <h2 class="text-sm font-medium leading-6 text-white">{{ $section->name }}</h2>
        </div>
        <div class="border-t border-gray-200 h-full flex flex-col">
            <div class="min-w-full overflow-hidden">
                <div class="bg-gray-50 flex">
                    <div class="flex w-1/2 pl-4 sm:pl-6">
                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-2/12">#
                        </div>
                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-10/12">Name
                        </div>
                    </div>
                    <div class="flex w-1/2">
                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-4/12 text-center">Pl
                        </div>
                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-4/12 text-center">W
                        </div>
                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-4/12 text-center">L
                        </div>
                    </div>
                </div>
                <div class="bg-white">
                    <!-- if there are players, otherwise show empty state -->
                    @if (!$players)
                        <div class="text-center m-4 p-4 rounded-lg border-2 border-dashed border-gray-300">
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">No frames</h3>
                            <p class="mt-1 text-sm text-gray-500 max-w-prose mx-auto">There have been no frames played
                                in this section yet. Please check back here again soon.</p>
                        </div>
                    @else
                        @foreach ($players as $player)
                            <a class="flex w-full border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50"
                                href="{{ route('player.show', $player->id) }}">
                                <div class="flex w-1/2 pl-4 sm:pl-6 items-center">
                                    <div
                                        class="whitespace-nowrap py-2 text-sm text-gray-900 w-2/12 font-semibold">
                                        {{ $loop->iteration + ($page - 1) * 10 }}.</div>
                                    <div class="whitespace-nowrap py-2 text-sm text-gray-900 w-10/12 flex gap-x-3 flex-col">
                                        {{ $player->name }}
                                    </div>
                                </div>
                                <div class="flex w-1/2 items-center">
                                    <div class="py-2 text-sm text-gray-900 w-4/12 text-center">
                                        {{ $player->frames_played }}
                                    </div>
                                    <div class="py-2 text-sm text-gray-900 w-4/12 text-center relative">
                                        {{ $player->frames_won }}
                                        <span class="items-center rounded-md bg-green-100 px-1 text-[10px] font-semibold text-green-700 absolute left-full -translate-x-1/2">{{ $player->frames_won_pct !== null ? rtrim(rtrim(number_format($player->frames_won_pct, 1), '0'), '.') : '0' }}%</span>
                                    </div>
                                    <div class="py-2 text-sm text-gray-900 w-4/12 text-center">
                                        {{ $player->frames_lost }}
                                    </div>
                                </div>
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
                    aria-label="Previous" {{ $page == 1 ? 'disabled' : '' }}>
                    &laquo; Previous
                </button>

                <button wire:click="nextPage" wire:loading.attr="disabled"
                    class="ml-auto inline-flex items-center px-2 py-1 border border-green-700/20 text-sm font-medium rounded-md text-white bg-green-700 hover:bg-green-700 disabled:opacity-50"
                    aria-label="Next" {{ $players && count($players) < 10 ? 'disabled' : '' }}>
                    Next &raquo;
                </button>

            </div>
        </div>
    </div>
</section>
