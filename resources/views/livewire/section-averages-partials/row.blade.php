@if ($row['can_link'])
    <a class="ui-average-item group"
        wire:key="section-average-{{ $section->id }}-page-{{ $page }}-player-{{ $row['player']->id }}"
        data-section-averages-row-type="link"
        data-section-averages-band
        data-slot="item"
        data-variant="muted"
        data-size="default"
        href="{{ route('player.show', $row['player']->id) }}">
@else
    <div class="ui-average-item"
        wire:key="section-average-{{ $section->id }}-page-{{ $page }}-player-{{ $row['player']->id }}"
        data-section-averages-row-type="static"
        data-section-averages-band
        data-slot="item"
        data-variant="muted"
        data-size="default">
@endif
    <div class="ui-average-item-content" data-slot="item-content">
        <div class="flex min-w-0 items-center gap-3">
            <span class="w-4 shrink-0 text-center text-sm font-semibold tabular-nums text-muted-foreground sm:w-7">
                {{ $row['ranking'] }}
            </span>
            <img class="size-8 shrink-0 rounded-full object-cover"
                src="{{ $row['player']->avatar_url }}"
                alt="{{ $row['player']->name }} avatar">
            <div class="min-w-0">
                <span class="block truncate text-sm font-semibold text-neutral-950 dark:text-neutral-50">{{ $row['player']->name }}</span>
                @if ($row['player']->team_name)
                    <p class="mt-1 truncate text-xs text-muted-foreground">{{ $row['player']->team_name }}</p>
                @endif
            </div>
        </div>

        <div class="ui-average-item-stats" data-slot="item-actions">
            <div class="w-12 sm:w-16">
                <div class="flex flex-col items-center gap-1">
                    <p class="text-sm font-semibold text-neutral-950 dark:text-neutral-50">{{ $row['player']->frames_played }}</p>
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
            <div class="hidden w-12 sm:block sm:w-16">
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
