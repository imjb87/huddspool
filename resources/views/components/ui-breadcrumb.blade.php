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

<nav {{ $attributes->class('ui-breadcrumb-scroll min-w-0 max-w-full overflow-x-auto') }} aria-label="Breadcrumb">
    <ol class="flex min-w-max flex-nowrap items-center gap-1.5 text-sm text-muted-foreground sm:gap-2.5">
        @foreach ($items as $item)
            @if (! $loop->first)
                <li role="presentation" aria-hidden="true" class="inline-flex items-center [&>svg]:size-3.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M9 6l6 6l-6 6" />
                    </svg>
                </li>
            @endif

            <li class="inline-flex items-center gap-1.5">
                @if (filled($item['url']) && ! $item['current'])
                    <a href="{{ $item['url'] }}"
                        class="block max-w-40 truncate transition-colors hover:text-foreground sm:max-w-none">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span @if ($item['current']) aria-current="page" class="block max-w-56 truncate font-normal text-foreground sm:max-w-none" @else class="block max-w-40 truncate" @endif>
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
