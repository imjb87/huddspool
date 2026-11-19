@props([
    'variant' => 'primary',
    'size' => 'default',
    'type' => 'button',
    'href' => null,
])

@php
    $variantClasses = [
        'primary' => 'ui-result-button ui-result-button-primary',
        'secondary' => 'ui-result-button ui-result-button-secondary',
    ];

    $sizeClasses = [
        'default' => '',
        'sm' => 'h-8 px-3 text-xs',
        'lg' => 'h-10 px-5',
    ];

    $buttonClasses = trim(implode(' ', [
        $variantClasses[$variant] ?? $variantClasses['primary'],
        $sizeClasses[$size] ?? $sizeClasses['default'],
    ]));
@endphp

@if (filled($href))
    <a href="{{ $href }}" {{ $attributes->class($buttonClasses) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($buttonClasses) }}>
        {{ $slot }}
    </button>
@endif
