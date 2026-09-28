@props([
    'size' => 'md',
    'variant' => 'primary',
    'text' => null,
    'class' => ''
])

@php
    $sizeClasses = [
        'xs' => 'w-3 h-3',
        'sm' => 'w-4 h-4', 
        'md' => 'w-6 h-6',
        'lg' => 'w-8 h-8',
        'xl' => 'w-12 h-12'
    ];

    $variantClasses = [
        'primary' => 'text-blue-600',
        'secondary' => 'text-gray-600',
        'success' => 'text-green-600',
        'warning' => 'text-yellow-600',
        'danger' => 'text-red-600',
        'info' => 'text-cyan-600',
        'light' => 'text-gray-300',
        'dark' => 'text-gray-800',
        'white' => 'text-white'
    ];

    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $variantClass = $variantClasses[$variant] ?? $variantClasses['primary'];
@endphp

<div class="inline-flex items-center {{ $class }}">
    <x-basics.icon name="ph-spinner-gap" class="animate-spin {{ $variantClass }}" />
    
    @if($text)
        <span class="ml-2 text-sm font-medium text-gray-700">{{ $text }}</span>
    @endif
</div>
