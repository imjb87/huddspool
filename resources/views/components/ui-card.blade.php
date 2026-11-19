@props([
    'as' => 'div',
    'variant' => 'default',
])

@php
    $variantClasses = [
        'default' => 'ui-card',
        'branded' => 'ui-card-branded',
        'panel' => 'ui-panel',
    ];

    $cardClasses = $variantClasses[$variant] ?? $variantClasses['default'];
@endphp

<{{ $as }} {{ $attributes->class($cardClasses) }}>
    {{ $slot }}
</{{ $as }}>
