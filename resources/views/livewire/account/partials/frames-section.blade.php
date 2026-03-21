@if ($this->frames->isNotEmpty())
    <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-account-frames-section>
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Frames</h3>
                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Recent frames you have played this season.
                </p>
            </div>

            <div class="lg:col-span-2">
                <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                    @foreach ($this->frameRows as $frameRow)
                        <a href="{{ route('result.show', $frameRow->result_id) }}"
                            class="group block"
                            wire:key="account-frame-{{ $frameRow->result_id }}-{{ $loop->index }}">
                            <div class="flex items-center gap-4 rounded-lg py-4 transition sm:px-3 group-hover:bg-gray-50 dark:group-hover:bg-zinc-800/70">
                                <div class="shrink-0">
                                    <span class="inline-flex h-7 min-w-[28px] items-center justify-center rounded-full px-2 text-xs font-bold text-white shadow-sm ring-1 ring-black/10 {{ $frameRow->result_pill_classes }}">
                                        {{ $frameRow->won_frame ? 'W' : 'L' }}
                                    </span>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $frameRow->opponent_name }}</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $frameRow->opponent_team }}</p>
                                </div>

                                <div class="shrink-0 text-right text-sm text-gray-500 dark:text-gray-400">
                                    {{ $frameRow->fixture_date_label }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
