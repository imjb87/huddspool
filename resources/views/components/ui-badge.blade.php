@props([
    'variant' => 'default',
])

@php
    $variantClasses = [
        'default' => 'border-transparent bg-foreground text-background shadow-xs',
        'secondary' => 'border-transparent bg-muted text-foreground',
        'outline' => 'border-border bg-background text-foreground',
    ];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background',
    $variantClasses[$variant] ?? $variantClasses['default'],
]) }}>
    {{ $slot }}
</span>
