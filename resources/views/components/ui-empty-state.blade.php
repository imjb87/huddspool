@props([
    'title',
    'description' => null,
    'layout' => 'default',
])

@php
    $layoutClasses = $layout === 'search'
        ? 'min-h-80 px-6 py-6'
        : 'min-h-40 px-6 py-8';
@endphp

<div {{ $attributes->class(["flex {$layoutClasses} flex-col items-center justify-center gap-1 text-center"]) }}>
    <h3 class="text-sm leading-5 font-medium text-foreground">{{ $title }}</h3>

    @if ($description)
        <p class="max-w-sm text-sm leading-5 text-muted-foreground">{{ $description }}</p>
    @endif

    {{ $slot }}
</div>
