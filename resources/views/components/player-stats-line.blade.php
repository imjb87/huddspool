@props([
    'href' => null,
    'avatarUrl',
    'name',
    'roleLabel' => null,
    'framesPlayed' => 0,
    'framesWon' => 0,
    'framesLost' => 0,
    'wrapperClass' => 'group block',
    'rowClass' => 'flex items-center gap-3 rounded-lg py-4 transition sm:-mx-3 sm:-my-px sm:gap-4 sm:px-3 sm:group-hover:bg-gray-200/70 dark:sm:group-hover:bg-neutral-900/70',
    'showInlineStatLabels' => true,
    'statsMarker' => null,
    'contentClass' => null,
    'statsClass' => 'ui-team-player-stats',
    'hideLostOnMobile' => false,
    'wireKey' => null,
])

@php
    $tag = $href ? 'a' : 'div';
    $attributes = $attributes->class($wrapperClass)->merge([
        'data-player-stats-line' => true,
    ]);

    if ($href) {
        $attributes = $attributes->merge(['href' => $href]);
    }

    if ($wireKey) {
        $attributes = $attributes->merge(['wire:key' => $wireKey]);
    }
@endphp

<{{ $tag }} {{ $attributes }}>
    <div class="{{ $rowClass }}">
        @if ($contentClass)
            <div class="{{ $contentClass }}" data-slot="item-content">
        @endif
            <div class="shrink-0">
                <img class="size-8 rounded-full object-cover"
                    src="{{ $avatarUrl }}"
                    alt="{{ $name }} avatar">
            </div>

            <div class="min-w-0 flex-1">
                <p class="truncate whitespace-nowrap text-sm font-semibold text-neutral-950 dark:text-neutral-50">{{ $name }}</p>
                @if ($roleLabel)
                    <p class="mt-1 text-xs text-muted-foreground">{{ $roleLabel }}</p>
                @endif
            </div>

            <div @if($statsMarker) data-{{ $statsMarker }} @endif class="{{ $statsClass }}" data-slot="item-actions">
                <div class="w-12 sm:w-16">
                    @if ($showInlineStatLabels)
                        <p class="text-xs font-medium text-muted-foreground">Played</p>
                    @endif
                    <div class="{{ $showInlineStatLabels ? 'mt-1 ' : '' }}flex flex-col items-center gap-1">
                        <p class="text-sm font-semibold text-neutral-950 dark:text-neutral-50">{{ (int) $framesPlayed }}</p>
                        <span class="invisible inline-flex items-center justify-center rounded-md px-1.5 py-0.5 text-[10px] font-semibold sm:text-xs">0%</span>
                    </div>
                </div>
                <div class="w-12 sm:w-16">
                    @if ($showInlineStatLabels)
                        <p class="text-xs font-medium text-muted-foreground">Won</p>
                    @endif
                    <div class="{{ $showInlineStatLabels ? 'mt-1 ' : '' }}flex flex-col items-center gap-1">
                        <p class="text-sm font-semibold text-green-700 dark:text-green-400">{{ (int) $framesWon }}</p>
                        <span class="inline-flex items-center justify-center rounded-md bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-950/50 dark:text-green-300 sm:text-xs">{{ \App\Support\PercentageFormatter::ratio((int) $framesWon, (int) $framesPlayed) }}%</span>
                    </div>
                </div>
                <div class="{{ $hideLostOnMobile ? 'hidden sm:block ' : '' }}w-12 sm:w-16">
                    @if ($showInlineStatLabels)
                        <p class="text-xs font-medium text-muted-foreground">Lost</p>
                    @endif
                    <div class="{{ $showInlineStatLabels ? 'mt-1 ' : '' }}flex flex-col items-center gap-1">
                        <p class="text-sm font-semibold text-red-700 dark:text-red-400">{{ (int) $framesLost }}</p>
                        <span class="inline-flex items-center justify-center rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-950/50 dark:text-red-300 sm:text-xs">{{ \App\Support\PercentageFormatter::ratio((int) $framesLost, (int) $framesPlayed) }}%</span>
                    </div>
                </div>
            </div>
        @if ($contentClass)
            </div>
        @endif
    </div>
</{{ $tag }}>
