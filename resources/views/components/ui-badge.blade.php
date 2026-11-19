@props([
    'variant' => 'default',
])

@php
    $variantClasses = [
        'default' => 'border-transparent bg-foreground text-background shadow-xs',
        'secondary' => 'border-transparent bg-muted text-foreground',
        'outline' => 'border-border bg-background text-foreground',
        'info' => 'border-info/30 bg-info/10 text-info-foreground dark:bg-info/15',
        'success' => 'border-success/30 bg-success/10 text-success-foreground dark:bg-success/15',
        'warning' => 'border-warning/30 bg-warning/10 text-warning-foreground dark:bg-warning/15',
        'destructive' => 'border-destructive/30 bg-destructive/10 text-destructive-foreground dark:bg-destructive/15',
    ];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background',
    $variantClasses[$variant] ?? $variantClasses['default'],
]) }}>
    {{ $slot }}
</span>
