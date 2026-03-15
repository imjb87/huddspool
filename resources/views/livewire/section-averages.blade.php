<section data-section-averages-view class="mt-0">
    <div class="w-full overflow-hidden border-y border-gray-200 bg-white shadow-md" data-section-averages-shell>
        <div class="min-w-full overflow-hidden">
            <div class="bg-linear-to-b from-gray-50 to-gray-100">
                <div class="mx-auto flex w-full max-w-4xl" data-section-averages-band>
                    <div class="flex w-[56%] pl-4 sm:w-1/2 sm:pl-6">
                        <div scope="col" class="w-2/12 py-2 text-sm font-semibold text-gray-900">#</div>
                        <div scope="col" class="w-10/12 py-2 text-sm font-semibold text-gray-900">Name</div>
                    </div>
                    <div class="grid w-[44%] grid-cols-[1fr_1fr_1fr_3.25rem] items-center gap-x-1 pr-4 sm:w-1/2 sm:pr-0">
                        <div scope="col" class="py-2 text-center text-sm font-semibold text-gray-900">Pl</div>
                        <div scope="col" class="py-2 text-center text-sm font-semibold text-gray-900">W</div>
                        <div scope="col" class="py-2 text-center text-sm font-semibold text-gray-900">L</div>
                        <div scope="col" class="py-2 text-center text-sm font-semibold text-gray-900">%</div>
                    </div>
                </div>
            </div>

            <div class="bg-white" wire:loading.remove wire:target="previousPage, nextPage">
                @if ($players->isEmpty())
                    <div class="px-4 py-10 text-center sm:px-6">
                        <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 px-6 py-8">
                            <h3 class="text-sm font-semibold text-gray-900">No frames</h3>
                            <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500">
                                There have been no frames played in this section yet. Please check back here again soon.
                            </p>
                        </div>
                    </div>
                @else
                    @foreach ($players as $player)
                        <a class="block w-full border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50"
                            wire:key="section-average-{{ $section->id }}-page-{{ $page }}-player-{{ $player->id }}"
                            href="{{ route('player.show', $player->id) }}">
                            <div class="mx-auto flex w-full max-w-4xl" data-section-averages-band>
                                <div class="flex w-[56%] items-center pl-4 sm:w-1/2 sm:pl-6">
                                    <div class="w-2/12 whitespace-nowrap py-2 text-sm font-semibold text-gray-900">
                                        {{ $loop->iteration + ($page - 1) * $perPage }}
                                    </div>
                                    <div class="flex w-10/12 items-center gap-x-3 whitespace-nowrap py-2 text-sm text-gray-900">
                                        <img class="h-6 w-6 rounded-full object-cover"
                                            src="{{ $player->avatar_url }}"
                                            alt="{{ $player->name }} avatar">
                                        <span class="truncate">{{ $player->name }}</span>
                                    </div>
                                </div>

                                <div class="grid w-[44%] grid-cols-[1fr_1fr_1fr_3.25rem] items-center gap-x-1 pr-4 sm:w-1/2 sm:pr-0">
                                    <div class="py-2 text-center text-sm text-gray-900">
                                        {{ $player->frames_played }}
                                    </div>
                                    <div class="py-2 text-center text-sm text-gray-900">
                                        <span>{{ $player->frames_won }}</span>
                                    </div>
                                    <div class="py-2 text-center text-sm text-gray-900">
                                        {{ $player->frames_lost }}
                                    </div>
                                    <div class="flex justify-center py-2">
                                        <span data-section-averages-percentage-badge
                                            class="inline-flex items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-1.5 py-1 text-[10px] font-semibold leading-none text-white shadow-sm ring-1 ring-black/10 sm:text-xs">
                                            {{ rtrim(rtrim(number_format($player->frames_won_percentage, 1), '0'), '.') }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>

            <div class="animate-pulse bg-white" wire:loading.block wire:target="previousPage, nextPage" data-section-averages-row-skeleton>
                @foreach (range(1, 10) as $row)
                    <div class="border-t border-gray-300" data-section-averages-row-skeleton-row>
                        <div class="mx-auto flex w-full max-w-4xl items-center" data-section-averages-band>
                            <div class="flex w-[56%] items-center pl-4 sm:w-1/2 sm:pl-6">
                                <div class="w-2/12 py-2">
                                    <div class="h-4 w-4 rounded-full bg-gray-200"></div>
                                </div>
                                <div class="flex w-10/12 items-center gap-3 py-2">
                                    <div class="h-6 w-6 rounded-full bg-gray-200"></div>
                                    <div class="h-4 w-32 rounded-full bg-gray-200"></div>
                                </div>
                            </div>

                            <div class="grid w-[44%] grid-cols-[1fr_1fr_1fr_3.25rem] items-center gap-x-1 pr-4 sm:w-1/2 sm:pr-0">
                                <div class="py-2 text-center">
                                    <div class="mx-auto h-4 w-6 rounded-full bg-gray-200"></div>
                                </div>
                                <div class="py-2 text-center">
                                    <div class="mx-auto h-4 w-6 rounded-full bg-gray-200"></div>
                                </div>
                                <div class="py-2 text-center">
                                    <div class="mx-auto h-4 w-6 rounded-full bg-gray-200"></div>
                                </div>
                                <div class="flex justify-center py-2">
                                    <div class="h-6 w-11 rounded-full bg-gray-200"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mx-auto w-full max-w-4xl pl-4 py-4 pr-4 sm:pl-6 sm:pr-6 lg:pl-6 lg:pr-0 lg:py-6" data-section-averages-controls>
        <div class="flex w-full" data-section-averages-band>
            <div class="flex w-[41%] justify-start">
                <button wire:click="previousPage" wire:loading.attr="disabled"
                    class="inline-flex w-24 cursor-pointer items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-3 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                    aria-label="Previous"
                    {{ $page == 1 ? 'disabled' : '' }}>
                    Previous
                </button>
            </div>

            <div class="flex w-[18%] items-center justify-center">
                <span class="text-sm font-semibold text-gray-900">
                    Page {{ $page }}
                </span>
            </div>

            <div class="flex w-[41%] justify-end">
                <button wire:click="nextPage" wire:loading.attr="disabled"
                    class="inline-flex w-24 cursor-pointer items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-3 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                    aria-label="Next"
                    {{ $players->count() < $perPage ? 'disabled' : '' }}>
                    Next
                </button>
            </div>
        </div>
    </div>
</section>
