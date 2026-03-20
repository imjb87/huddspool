<section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-result-card-section>
    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Result card</h3>
            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Frame-by-frame scores, match totals, and submission details.
            </p>
        </div>

        <div class="space-y-5 lg:col-span-2">
            @if (! $result->is_confirmed)
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 dark:border-yellow-900/60 dark:bg-yellow-950/30">
                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                        This result is still in progress and will remain editable until it is locked.
                    </p>
                </div>
            @endif

            @if ($result->is_overridden)
                <div class="px-4 py-10 text-center sm:px-6">
                    <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 px-6 py-8 dark:border-zinc-700 dark:bg-zinc-800/75">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Result overridden</h3>
                        <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                            This match result was overridden by an admin.
                        </p>
                    </div>
                </div>
            @else
                <div class="space-y-4" data-result-card-shell>
                    <div class="divide-y divide-gray-200 dark:divide-zinc-800/80" data-result-card-frames>
                        @foreach ($result->frames as $key => $frame)
                            <div class="py-4" wire:key="result-frame-{{ $frame->id }}">
                                <div class="space-y-3" data-result-card-band>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Frame {{ $key + 1 }}
                                    </p>

                                    <div class="flex items-center gap-3">
                                        <div class="min-w-0 flex-1">
                                            @if ($frame->home_player_id)
                                                <a href="{{ route('player.show', $frame->homePlayer) }}"
                                                    class="inline-flex min-w-0 items-center gap-2 text-sm font-medium text-gray-900 transition hover:text-gray-500 dark:text-gray-100 dark:hover:text-gray-300">
                                                    <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                                                        src="{{ $frame->homePlayer->avatar_url }}"
                                                        alt="{{ $frame->homePlayer->name }} avatar">
                                                    <span class="truncate">{{ $frame->homePlayer->name }}</span>
                                                </a>
                                            @else
                                                <span class="inline-flex min-w-0 items-center gap-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                                                        src="{{ asset('/images/user.jpg') }}"
                                                        alt="Awarded">
                                                    <span class="truncate">Awarded</span>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="shrink-0">
                                            <div class="inline-flex h-7 w-9 items-center justify-center overflow-hidden rounded-full bg-gray-100 text-center text-xs font-extrabold text-gray-700 ring-1 ring-gray-200 dark:bg-zinc-800 dark:text-gray-200 dark:ring-zinc-700"
                                                data-result-frame-score-pill>
                                                {{ $frame->home_score }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="min-w-0 flex-1">
                                            @if ($frame->away_player_id)
                                                <a href="{{ route('player.show', $frame->awayPlayer) }}"
                                                    class="inline-flex min-w-0 items-center gap-2 text-sm font-medium text-gray-900 transition hover:text-gray-500 dark:text-gray-100 dark:hover:text-gray-300">
                                                    <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                                                        src="{{ $frame->awayPlayer->avatar_url }}"
                                                        alt="{{ $frame->awayPlayer->name }} avatar">
                                                    <span class="truncate">{{ $frame->awayPlayer->name }}</span>
                                                </a>
                                            @else
                                                <span class="inline-flex min-w-0 items-center gap-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                                                        src="{{ asset('/images/user.jpg') }}"
                                                        alt="Awarded">
                                                    <span class="truncate">Awarded</span>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="shrink-0">
                                            <div class="inline-flex h-7 w-9 items-center justify-center overflow-hidden rounded-full bg-gray-100 text-center text-xs font-extrabold text-gray-700 ring-1 ring-gray-200 dark:bg-zinc-800 dark:text-gray-200 dark:ring-zinc-700"
                                                data-result-frame-score-pill>
                                                {{ $frame->away_score }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-start justify-between gap-4 py-1" data-result-card-band>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Match total</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ $result->home_team_name }}
                                <span class="text-gray-300 dark:text-zinc-600">/</span>
                                {{ $result->away_team_name }}
                            </p>
                        </div>

                        <div class="ml-auto flex shrink-0 self-center items-center text-right">
                            <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10"
                                data-result-score-pill>
                                <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">{{ $result->home_score }}</div>
                                <div class="w-px bg-white/25"></div>
                                <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">{{ $result->away_score }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($result->is_confirmed && $result->submittedBy)
                @php
                    $submittedAt = $result->submitted_at ?? $result->created_at;
                @endphp
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Submitted by {{ $result->submittedBy->name }} on {{ $submittedAt->format('j M Y') }} at {{ $submittedAt->format('H:i') }}.
                </p>
            @endif
        </div>
    </div>
</section>
