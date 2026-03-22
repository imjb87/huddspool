@if ($knockoutRows->isNotEmpty())
    <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-player-knockout-section>
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Knockouts</h3>
                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Knockout matches this player has featured in.
                </p>
            </div>

            <div class="lg:col-span-2">
                <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                    @foreach ($knockoutRows as $knockoutRow)
                        <div wire:key="player-knockout-{{ $knockoutRow->id }}">
                            @if ($knockoutRow->row_url)
                                <a href="{{ $knockoutRow->row_url }}" class="block py-4 transition sm:rounded-xl sm:px-3 sm:hover:bg-gray-200/70 dark:sm:hover:bg-zinc-800/70">
                            @endif
                            <div class="flex items-start gap-3 {{ $knockoutRow->row_url ? '' : 'py-4 sm:rounded-xl sm:px-3' }} sm:items-center sm:gap-4">
                                <div class="min-w-0 flex-1">
                                    @if ($knockoutRow->is_doubles)
                                        <div class="space-y-0.5 text-sm leading-5 font-semibold text-gray-900 dark:text-gray-100">
                                            <p class="[overflow-wrap:anywhere]">
                                                <span>{{ $knockoutRow->home_label }}</span>
                                                <span class="px-1 font-normal text-gray-400 dark:text-zinc-500">vs</span>
                                            </p>
                                            <p class="[overflow-wrap:anywhere] text-gray-700 dark:text-gray-300">
                                                {{ $knockoutRow->away_label }}
                                            </p>
                                        </div>
                                    @else
                                        <p class="[overflow-wrap:anywhere] text-sm leading-5 font-semibold text-gray-900 dark:text-gray-100">
                                            <span>{{ $knockoutRow->home_label }}</span>
                                            <span class="px-1 font-normal text-gray-400 dark:text-zinc-500">vs</span>
                                            <span>{{ $knockoutRow->away_label }}</span>
                                        </p>
                                    @endif
                                    <p class="mt-1 [overflow-wrap:anywhere] text-xs leading-5 text-gray-500 dark:text-gray-400">
                                        {{ $knockoutRow->meta_label }}
                                    </p>
                                </div>

                                <div class="shrink-0 text-right">
                                    @if ($knockoutRow->has_result)
                                        <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full {{ $knockoutRow->result_pill_classes }} text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10">
                                            <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">{{ $knockoutRow->home_score }}</div>
                                            <div class="w-px bg-white/25"></div>
                                            <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">{{ $knockoutRow->away_score }}</div>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $knockoutRow->date_label }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @if ($knockoutRow->row_url)
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
