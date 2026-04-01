@if ($this->teamKnockoutMatches->isNotEmpty())
    <section class="ui-section" data-account-team-knockout-section>
        <div class="ui-shell-grid">
            <div class="ui-section-intro">
                <div class="ui-section-intro-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                    </svg>
                </div>
                <div class="ui-section-intro-copy">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Team knockouts</h3>
                    <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                        Your team's recent knockout matches and any ties that still need a result submitting.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card">
                    <div class="ui-card-rows">
                    @foreach ($this->teamKnockoutMatches as $knockoutRow)
                        <div wire:key="account-team-knockout-{{ $knockoutRow->id }}">
                            @if ($knockoutRow->row_url)
                                <a href="{{ $knockoutRow->row_url }}" class="ui-card-row-link">
                            @endif
                            <div class="ui-card-row items-start px-4 sm:px-5">
                                <div class="min-w-0 flex-1">
                                    <p class="mb-1 text-xs leading-5 text-gray-500 dark:text-gray-400">
                                        {{ $knockoutRow->meta_label }}
                                    </p>

                                    <p class="[overflow-wrap:anywhere] text-sm leading-5 font-semibold text-gray-900 dark:text-gray-100">
                                        <span>{{ $knockoutRow->home_label }}</span>
                                        <span class="px-1 font-normal text-gray-400 dark:text-gray-500">vs</span>
                                        <span>{{ $knockoutRow->away_label }}</span>
                                    </p>

                                    <p class="mt-1 [overflow-wrap:anywhere] text-xs leading-5 text-gray-500 dark:text-gray-400">
                                        <span>Venue: </span>
                                        <span>{{ $knockoutRow->venue_label ?? 'Venue TBC' }}</span>
                                    </p>

                                    @if ($knockoutRow->referee_label ?? null)
                                        <p class="[overflow-wrap:anywhere] text-xs leading-5 text-gray-500 dark:text-gray-400">
                                            <span>Referee: </span>
                                            <span>{{ $knockoutRow->referee_label }}</span>
                                        </p>
                                    @endif
                                </div>

                                <div class="shrink-0 self-center text-right">
                                    @if ($knockoutRow->has_result)
                                        <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full {{ $knockoutRow->result_pill_classes }} text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10">
                                            <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">
                                                {{ $knockoutRow->home_score }}
                                            </div>
                                            <div class="w-px bg-white/25"></div>
                                            <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">
                                                {{ $knockoutRow->away_score }}
                                            </div>
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
        </div>
    </section>
@endif
