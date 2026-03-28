@props([
    'href' => null,
    'avatarUrl',
    'name',
    'roleLabel' => null,
    'framesPlayed' => 0,
    'framesWon' => 0,
    'framesLost' => 0,
    'wrapperClass' => 'group block',
    'statsMarker' => null,
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
    <div class="flex items-center gap-3 rounded-lg py-4 transition sm:-mx-3 sm:-my-px sm:gap-4 sm:px-3 sm:group-hover:bg-gray-200/70 dark:sm:group-hover:bg-zinc-800/70">
        <div class="shrink-0">
            <img class="h-8 w-8 rounded-full object-cover"
                src="{{ $avatarUrl }}"
                alt="{{ $name }} avatar">
        </div>

        <div class="min-w-0 flex-1">
            <p class="[overflow-wrap:anywhere] text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $name }}</p>
            @if ($roleLabel)
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $roleLabel }}</p>
            @endif
        </div>

        <div @if($statsMarker) data-{{ $statsMarker }} @endif class="ml-auto flex shrink-0 items-center gap-2 text-center sm:gap-5">
            <div class="w-14 sm:w-20">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Played</p>
                <div class="mt-1 flex flex-col items-center gap-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ (int) $framesPlayed }}</p>
                    <span class="invisible inline-flex items-center rounded-md px-1 py-0.5 text-[10px] font-semibold">0%</span>
                </div>
            </div>
            <div class="w-14 sm:w-20">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Won</p>
                <div class="mt-1 flex flex-col items-center gap-1">
                    <p class="text-sm font-semibold text-green-700 dark:text-green-400">{{ (int) $framesWon }}</p>
                    <span class="inline-flex items-center rounded-md bg-green-100 px-1 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-950/50 dark:text-green-300">{{ \App\Support\PercentageFormatter::ratio((int) $framesWon, (int) $framesPlayed) }}%</span>
                </div>
            </div>
            <div class="w-14 sm:w-20">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Lost</p>
                <div class="mt-1 flex flex-col items-center gap-1">
                    <p class="text-sm font-semibold text-red-700 dark:text-red-400">{{ (int) $framesLost }}</p>
                    <span class="inline-flex items-center rounded-md bg-red-100 px-1 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-950/50 dark:text-red-300">{{ \App\Support\PercentageFormatter::ratio((int) $framesLost, (int) $framesPlayed) }}%</span>
                </div>
            </div>
        </div>
    </div>
</{{ $tag }}>
