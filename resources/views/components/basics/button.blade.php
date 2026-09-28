@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md', // xs, sm, md, lg, xl
    'disabled' => false,
    'loading' => false,
    'loadingText' => null,
    'leftIcon' => null,
    'rightIcon' => null,
    'icon' => null,
    'iconPosition' => 'left',
    'class' => '',
    'badge' => null,
    'badgeColor' => 'red',
    'wireTarget' => null,  // For Livewire loading states
    'jsLoading' => false,  // For JavaScript loading states
])

@php
    // Base button classes - consistent with input/select components
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-lg shadow-sm transition-all duration-200 focus:ring-2 focus:ring-inset disabled:opacity-60 disabled:cursor-not-allowed disabled:bg-gray-200 disabled:text-gray-500 cursor-pointer';
    
    // Variant classes with matching focus colors
    $variantClasses = match($variant) {
        'primary' => 'bg-brand-600 text-white hover:bg-brand-500 focus:ring-brand-600',
        'secondary' => 'bg-gray-600 text-white hover:bg-gray-500 focus:ring-gray-600',
        'success' => 'bg-green-600 text-white hover:bg-green-500 focus:ring-green-600',
        'danger' => 'bg-red-600 text-white hover:bg-red-500 focus:ring-red-600',
        'warning' => 'bg-yellow-600 text-white hover:bg-yellow-500 focus:ring-yellow-600',
        'info' => 'bg-blue-600 text-white hover:bg-blue-500 focus:ring-blue-600',
        'outline' => 'bg-transparent text-brand-600 ring-1 ring-inset ring-brand-600 hover:bg-brand-50 focus:ring-brand-600',
        'outline-red' => 'bg-transparent text-red-600 ring-1 ring-inset ring-red-600 hover:bg-red-50 focus:ring-red-600',
        'ghost' => 'bg-transparent text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-100 focus:ring-gray-600',
        default => 'bg-brand-600 text-white hover:bg-brand-500 focus:ring-brand-600',
    };
    
    // Size classes with standardized heights - matching input/select components
    $sizeClasses = match($size) {
        'xs' => 'px-2.5 py-1.5 text-xs h-8',
        'sm' => 'px-3 py-2 text-sm h-9',
        'md' => 'px-4 py-2.5 text-sm h-11',  // Same height as input/select
        'lg' => 'px-6 py-3 text-base h-12',
        'xl' => 'px-8 py-4 text-lg h-14',
        default => 'px-4 py-2.5 text-sm h-11',  // Same height as input/select
    };
    
    // Override disabled state for outline* and ghost variants
    if ($disabled && (str_starts_with($variant, 'outline') || $variant === 'ghost')) {
        // Neutralize rings, text color and remove hover/focus rings
        $variantClasses = preg_replace([
            '/ring-[a-z]+-\d+/',
            '/text-[a-z]+-\d+/',
            '/hover:bg-[a-z]+-\d+/',
            '/focus:ring-[a-z]+-\d+/'
        ], [
            'ring-gray-200',
            'text-gray-400',
            '',
            ''
        ], $variantClasses);
    }
    
    // Badge color variants
    $badgeColorClasses = match($badgeColor) {
        'red' => 'bg-red-500 text-white',
        'green' => 'bg-green-500 text-white',
        'blue' => 'bg-blue-500 text-white',
        'yellow' => 'bg-yellow-500 text-black',
        'indigo' => 'bg-brand-500 text-white',
        'gray' => 'bg-gray-500 text-white',
        default => 'bg-red-500 text-white',
    };
    
    // Combine all classes
    $buttonClasses = $baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses . ' ' . $class;
@endphp

<button 
    type="{{ $type }}"
    @if($disabled) disabled @endif
    {{ $attributes->merge(['class' => $buttonClasses]) }}
    @if($wireTarget) wire:loading.attr="disabled" wire:target="{{ $wireTarget }}" @endif
    @if($jsLoading) onclick="this.disabled=true; this.querySelector('.js-loading').classList.remove('hidden'); this.querySelector('.js-normal').classList.add('hidden'); this.form?.submit();" @endif
>
    {{-- Livewire loading state --}}
    @if($wireTarget)
        <div wire:loading.remove wire:target="{{ $wireTarget }}" class="flex items-center">
            @if($leftIcon)
                <i class="ph ph-{!! $leftIcon !!} mr-2"></i>
            @endif

            @if($icon && $iconPosition === 'left')
                <span class="mr-2">{!! $icon !!}</span>
            @endif
            
            <span>{{ $slot }}</span>
            
            @if($badge)
                <span class="ml-2 {{ $badgeColorClasses }} text-xs font-bold rounded-full h-4 w-4 flex items-center justify-center min-w-4">
                    {{ $badge }}
                </span>
            @endif
            
            @if($icon && $iconPosition === 'right')
                <span class="ml-2">{!! $icon !!}</span>
            @endif

            @if($rightIcon)
                <i class="ph ph-{!! $rightIcon !!} ml-2"></i>
            @endif
        </div>
        
        <div wire:loading wire:target="{{ $wireTarget }}" class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            @if($loadingText)
                <span>{{ $loadingText }}</span>
            @endif
        </div>
    @else
        {{-- Manual/JavaScript loading state --}}
        <div class="js-normal @if($loading) hidden @endif flex items-center">
            @if($leftIcon)
                <i class="ph ph-{!! $leftIcon !!} mr-2"></i>
            @endif

            @if($icon && $iconPosition === 'left')
                <span class="mr-2">{!! $icon !!}</span>
            @endif
            
            <span>{{ $slot }}</span>
            
            @if($badge)
                <span class="ml-2 {{ $badgeColorClasses }} text-xs font-bold rounded-full h-4 w-4 flex items-center justify-center min-w-4">
                    {{ $badge }}
                </span>
            @endif
            
            @if($icon && $iconPosition === 'right')
                <span class="ml-2">{!! $icon !!}</span>
            @endif

            @if($rightIcon)
                <i class="ph ph-{!! $rightIcon !!} ml-2"></i>
            @endif
        </div>
        
        <div class="js-loading @if(!$loading) hidden @endif flex items-center justify-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ $loadingText ?? 'Loading...' }}
        </div>
    @endif
</button> 