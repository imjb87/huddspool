@if ($row['can_link'])
    <a class="group block"
        wire:key="section-average-{{ $section->id }}-page-{{ $page }}-player-{{ $row['player']->id }}"
        data-section-averages-row-type="link"
        href="{{ route('player.show', $row['player']->id) }}">
@else
    <div class="block"
        wire:key="section-average-{{ $section->id }}-page-{{ $page }}-player-{{ $row['player']->id }}"
        data-section-averages-row-type="static">
@endif
    <div class="flex items-center gap-3 rounded-lg py-3 sm:gap-4 sm:px-3 sm:py-4 sm:transition sm:group-hover:bg-gray-50 dark:sm:group-hover:bg-zinc-800/70" data-section-averages-band>
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-3">
                <span class="w-4 shrink-0 text-center text-sm font-semibold tabular-nums text-gray-500 dark:text-gray-400 sm:w-7">
                    {{ $row['ranking'] }}
                </span>
                <img class="h-8 w-8 shrink-0 rounded-full object-cover"
                    src="{{ $row['player']->avatar_url }}"
                    alt="{{ $row['player']->name }} avatar">
                <div class="min-w-0">
                    <span class="block truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $row['player']->name }}</span>
                    @if ($row['player']->team_name)
                        <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">{{ $row['player']->team_name }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="ml-auto flex shrink-0 items-center gap-2 text-center sm:gap-5">
            <div class="w-12 sm:w-16">
                <div class="flex flex-col items-center gap-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $row['player']->frames_played }}</p>
                    <span class="invisible inline-flex items-center justify-center rounded-md px-1.5 py-0.5 text-[10px] font-semibold sm:text-xs">
                        0%
                    </span>
                </div>
            </div>
            <div class="w-12 sm:w-16">
                <div class="flex flex-col items-center gap-1">
                    <p class="text-sm font-semibold text-green-700 dark:text-green-400">{{ $row['player']->frames_won }}</p>
                    <span data-section-averages-percentage-badge
                        class="inline-flex items-center justify-center rounded-md bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-950/50 dark:text-green-300 sm:text-xs">
                        {{ \App\Support\PercentageFormatter::trimmedSingleDecimal($row['player']->frames_won_percentage) }}%
                    </span>
                </div>
            </div>
            <div class="w-12 sm:w-16">
                <div class="flex flex-col items-center gap-1">
                    <p class="text-sm font-semibold text-red-700 dark:text-red-400">{{ $row['player']->frames_lost }}</p>
                    <span class="inline-flex items-center justify-center rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-950/50 dark:text-red-300 sm:text-xs">
                        {{ \App\Support\PercentageFormatter::trimmedSingleDecimal($row['player']->frames_lost_percentage) }}%
                    </span>
                </div>
            </div>
        </div>
    </div>
@if ($row['can_link'])
    </a>
@else
    </div>
@endif
