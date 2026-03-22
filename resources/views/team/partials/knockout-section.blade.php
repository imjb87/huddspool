@if ($teamKnockoutMatches->isNotEmpty())
    <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-team-knockout-section>
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Team knockouts</h3>
                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Recent team knockout ties and completed results.
                </p>
            </div>

            <div class="lg:col-span-2">
                <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                    @foreach ($teamKnockoutRows as $knockoutRow)
                        <div wire:key="team-knockout-{{ $knockoutRow->id }}">
                            @if ($knockoutRow->row_url)
                                <a href="{{ $knockoutRow->row_url }}" class="block transition hover:bg-gray-200/70 sm:rounded-xl sm:px-3 sm:py-4 dark:hover:bg-zinc-800/70">
                            @endif
                            <div class="flex items-start gap-3 {{ $knockoutRow->row_url ? '' : 'sm:rounded-xl sm:px-3 sm:py-4' }} sm:items-center sm:gap-4">
                                <div class="min-w-0 flex-1">
                                    <p class="[overflow-wrap:anywhere] text-sm leading-5 font-semibold text-gray-900 dark:text-gray-100">
                                        <span>{{ $knockoutRow->home_label }}</span>
                                        <span class="px-1 font-normal text-gray-400 dark:text-gray-500">vs</span>
                                        <span>{{ $knockoutRow->away_label }}</span>
                                    </p>
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
