@props([
    'items' => [],
])

@php
    $items = collect($items)
        ->map(function ($item): array {
            if (is_string($item)) {
                return [
                    'label' => $item,
                    'url' => null,
                    'current' => true,
                ];
            }

            return [
                'label' => $item['label'],
                'url' => $item['url'] ?? null,
                'current' => (bool) ($item['current'] ?? false),
            ];
        })
        ->values();
@endphp

<nav {{ $attributes->class('flex flex-wrap items-center gap-x-2 text-xs text-gray-500 dark:text-gray-400') }} aria-label="Breadcrumb">
    @foreach ($items as $item)
        @if (! $loop->first)
            <span aria-hidden="true">/</span>
        @endif

        @if (filled($item['url']) && ! $item['current'])
            <a href="{{ $item['url'] }}"
                class="transition hover:text-gray-700 dark:hover:text-gray-200">
                {{ $item['label'] }}
            </a>
        @else
            <span @if ($item['current']) aria-current="page" class="text-gray-700 dark:text-gray-300" @endif>
                {{ $item['label'] }}
            </span>
        @endif
    @endforeach
</nav>
