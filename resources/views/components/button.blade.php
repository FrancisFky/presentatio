@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'loading' => false,
    'loadingText' => null,
    'icon' => null,
    'iconPosition' => 'left',
    'class' => '',
])

@php
    // Base button classes
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
    
    // Variant classes
    $variantClasses = match($variant) {
        'primary' => 'bg-brand-500 text-white shadow-sm hover:bg-brand-600 focus-visible:outline-brand-600',
        'secondary' => 'bg-gray-600 text-white shadow-sm hover:bg-gray-500 focus-visible:outline-gray-600',
        'success' => 'bg-green-600 text-white shadow-sm hover:bg-green-500 focus-visible:outline-green-600',
        'danger' => 'bg-red-600 text-white shadow-sm hover:bg-red-500 focus-visible:outline-red-600',
        'warning' => 'bg-yellow-600 text-white shadow-sm hover:bg-yellow-500 focus-visible:outline-yellow-600',
        'info' => 'bg-blue-600 text-white shadow-sm hover:bg-blue-500 focus-visible:outline-blue-600',
        'outline' => 'bg-transparent text-brand-600 border border-brand-600 hover:bg-brand-50 focus-visible:outline-brand-600',
        'ghost' => 'bg-transparent text-gray-700 hover:bg-gray-100 focus-visible:outline-gray-600',
        default => 'bg-brand-600 text-white shadow-sm hover:bg-brand-500 focus-visible:outline-brand-600',
    };
    
    // Size classes with standardized heights
    $sizeClasses = match($size) {
        'xs' => 'px-2.5 py-1.5 text-xs h-8',
        'sm' => 'px-3 py-2 text-sm h-9',
        'md' => 'px-4 py-2.5 text-sm h-11',
        'lg' => 'px-6 py-3 text-base h-12',
        'xl' => 'px-8 py-4 text-lg h-14',
        default => 'px-4 py-2.5 text-sm h-11',
    };
    
    // Combine all classes
    $buttonClasses = $baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses . ' ' . $class;
@endphp

<button 
    type="{{ $type }}"
    @if($disabled) disabled @endif
    {{ $attributes->merge(['class' => $buttonClasses]) }}
>
    @if($loading)
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @endif
    
    @if($icon && $iconPosition === 'left' && !$loading)
        <span class="mr-2">{!! $icon !!}</span>
    @endif
    
    <span>{{ $loading && $loadingText ? $loadingText : $slot }}</span>
    
    @if($icon && $iconPosition === 'right' && !$loading)
        <span class="ml-2">{!! $icon !!}</span>
    @endif
</button> 