<section data-section-averages-view class="mt-0">
    @php
        $isHistoryView = $history ?? false;
        $summaryCopy = $isHistoryView
            ? 'Archived frame records and win rates for this section.'
            : 'Current frame records and win rates for this section.';
    @endphp
    <div class="mx-auto mt-6 w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Averages</h2>
                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    {{ $summaryCopy }}
                </p>
            </div>

            <div class="lg:col-span-2">
                <div data-section-averages-shell>
                    <div class="flex items-center justify-between gap-2 pb-2" data-section-averages-band>
                        <div class="flex min-w-0 items-center gap-2 sm:gap-3"></div>

                        <div class="ml-auto flex shrink-0 items-start gap-2 text-center sm:gap-5">
                            <div class="w-12 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-16">Pl</div>
                            <div class="w-12 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-16">Won</div>
                            <div class="w-12 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-16">Lost</div>
                        </div>
                    </div>

                    <div wire:loading.remove wire:target="previousPage, nextPage">
                        @if ($players->isEmpty())
                            <div class="px-4 py-10 text-center sm:px-6">
                                <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 px-6 py-8 dark:border-zinc-700 dark:bg-zinc-800/75">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">No frames</h3>
                                    <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                                        There have been no frames played in this section yet. Please check back here again soon.
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                                @foreach ($players as $player)
                                    @php
                                        $canLinkPlayer = ! ($isHistoryView && $player->trashed);
                                        $ranking = $loop->iteration + ($page - 1) * $perPage;
                                        $winPercentage = rtrim(rtrim(number_format($player->frames_won_percentage, 1), '0'), '.');
                                        $lossPercentage = rtrim(rtrim(number_format($player->frames_lost_percentage, 1), '0'), '.');
                                    @endphp
                                    @if ($canLinkPlayer)
                                        <a class="block rounded-lg py-4"
                                            wire:key="section-average-{{ $section->id }}-page-{{ $page }}-player-{{ $player->id }}"
                                            data-section-averages-row-type="link"
                                            href="{{ route('player.show', $player->id) }}">
                                    @else
                                        <div class="block rounded-lg py-4"
                                            wire:key="section-average-{{ $section->id }}-page-{{ $page }}-player-{{ $player->id }}"
                                            data-section-averages-row-type="static">
                                    @endif
                                        <div class="flex items-center gap-3 sm:gap-4" data-section-averages-band>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-3">
                                                    <span class="shrink-0 text-sm font-semibold tabular-nums text-gray-500 dark:text-gray-400">
                                                        {{ $ranking }}
                                                    </span>
                                                    <img class="h-8 w-8 shrink-0 rounded-full object-cover"
                                                        src="{{ $player->avatar_url }}"
                                                        alt="{{ $player->name }} avatar">
                                                    <div class="min-w-0">
                                                        <span class="block truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $player->name }}</span>
                                                        @if ($player->team_name)
                                                            <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">{{ $player->team_name }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="ml-auto flex shrink-0 items-center gap-2 text-center sm:gap-5">
                                                <div class="w-12 sm:w-16">
                                                    <div class="flex flex-col items-center gap-1">
                                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $player->frames_played }}</p>
                                                        <span class="invisible inline-flex items-center justify-center rounded-md px-1.5 py-0.5 text-[10px] font-semibold sm:text-xs">
                                                            0%
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="w-12 sm:w-16">
                                                    <div class="flex flex-col items-center gap-1">
                                                        <p class="text-sm font-semibold text-green-700 dark:text-green-400">{{ $player->frames_won }}</p>
                                                        <span data-section-averages-percentage-badge
                                                            class="inline-flex items-center justify-center rounded-md bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-950/50 dark:text-green-300 sm:text-xs">
                                                            {{ $winPercentage }}%
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="w-12 sm:w-16">
                                                    <div class="flex flex-col items-center gap-1">
                                                        <p class="text-sm font-semibold text-red-700 dark:text-red-400">{{ $player->frames_lost }}</p>
                                                        <span class="inline-flex items-center justify-center rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-950/50 dark:text-red-300 sm:text-xs">
                                                            {{ $lossPercentage }}%
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @if ($canLinkPlayer)
                                        </a>
                                    @else
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="animate-pulse" wire:loading.block wire:target="previousPage, nextPage" data-section-averages-row-skeleton>
                        <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                            @foreach (range(1, 5) as $row)
                                <div class="py-4" data-section-averages-row-skeleton-row>
                                    <div class="flex items-center gap-3 sm:gap-4" data-section-averages-band>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-3">
                                                <div class="h-4 w-4 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                                <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                                <div class="h-4 w-28 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                            </div>
                                        </div>

                                        <div class="ml-auto flex shrink-0 items-center gap-2 sm:gap-5">
                                            <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-10"></div>
                                            <div class="flex flex-col items-center gap-1">
                                                <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-10"></div>
                                                <div class="h-6 w-12 rounded-md bg-gray-200 dark:bg-zinc-700"></div>
                                            </div>
                                            <div class="flex flex-col items-center gap-1">
                                                <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-10"></div>
                                                <div class="h-6 w-12 rounded-md bg-gray-200 dark:bg-zinc-700"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="pt-5 pb-4 lg:pt-5 lg:pb-6" data-section-averages-controls>
                    <div class="flex items-center justify-between gap-4" data-section-averages-band>
                        <button wire:click="previousPage" wire:loading.attr="disabled"
                            class="inline-flex w-24 cursor-pointer items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-3 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                            aria-label="Previous"
                            {{ $page == 1 ? 'disabled' : '' }}>
                            Previous
                        </button>

                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            Page {{ $page }}
                        </span>

                        <button wire:click="nextPage" wire:loading.attr="disabled"
                            class="inline-flex w-24 cursor-pointer items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-3 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                            aria-label="Next"
                            {{ $players->count() < $perPage ? 'disabled' : '' }}>
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
