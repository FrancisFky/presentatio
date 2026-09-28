@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => '#',
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'class' => '',
    'badge' => null,
    'badgeColor' => 'red',
    // Backward compatibility props
    'leftIcon' => null,
    'rightIcon' => null,
    'target' => null,
])

@php
    // Handle backward compatibility
    if ($leftIcon) {
        $icon = '<i class="ph ph-' . $leftIcon . '"></i>';
        $iconPosition = 'left';
    } elseif ($rightIcon) {
        $icon = '<i class="ph ph-' . $rightIcon . '"></i>';
        $iconPosition = 'right';
    }
    
    // Check if it's an external URL (starts with http:// or https://)
    $isExternal = !empty($href) && (str_starts_with($href, 'http://') || str_starts_with($href, 'https://'));
    
    // Convert old variant names to new ones
    $variant = match($variant) {
        'outlined' => 'outline',
        'filled' => 'primary',
        default => $variant,
    };
    
    // Base button-link classes - matching button component design
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-lg shadow-sm transition-all duration-200 focus:ring-1 focus:ring-inset no-underline';
    
    // Variant classes with matching focus colors - same as button component
    $variantClasses = match($variant) {
        'primary' => 'bg-brand-600 text-white hover:bg-brand-500 focus:ring-brand-600',
        'secondary' => 'bg-gray-600 text-white hover:bg-gray-500 focus:ring-gray-600',
        'success' => 'bg-green-600 text-white hover:bg-green-500 focus:ring-green-600',
        'danger' => 'bg-red-600 text-white hover:bg-red-500 focus:ring-red-600',
        'warning' => 'bg-yellow-600 text-white hover:bg-yellow-500 focus:ring-yellow-600',
        'info' => 'bg-blue-600 text-white hover:bg-blue-500 focus:ring-blue-600',
        'outline' => 'bg-transparent text-brand-600 ring-1 ring-inset ring-brand-600 hover:bg-brand-50 focus:ring-brand-600',
        'ghost' => 'bg-transparent text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-100 focus:ring-gray-600',
        default => 'bg-brand-600 text-white hover:bg-brand-500 focus:ring-brand-600',
    };
    
    // Size classes with standardized heights - matching button component
    $sizeClasses = match($size) {
        'xs' => 'px-2.5 py-1.5 text-xs h-8',
        'sm' => 'px-3 py-2 text-sm h-9',
        'md' => 'px-4 py-2.5 text-sm h-11',  // Same height as input/select
        'lg' => 'px-6 py-3 text-base h-12',
        'xl' => 'px-8 py-4 text-lg h-14',
        default => 'px-4 py-2.5 text-sm h-11',  // Same height as input/select
    };
    
    // Handle disabled state
    if ($disabled) {
        $baseClasses .= ' opacity-60 cursor-not-allowed pointer-events-none bg-gray-100 text-gray-500';
        // Override disabled state for outline and ghost variants
        if (in_array($variant, ['outline', 'ghost'])) {
            $variantClasses = str_replace(['ring-brand-600', 'ring-gray-300'], 'ring-gray-200', $variantClasses);
            // Remove focus ring for disabled state
            $variantClasses = preg_replace('/focus:ring-\w+-\d+/', '', $variantClasses);
        }
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
    $linkClasses = $baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses . ' ' . $class;
@endphp

<a 
    href="{{ $disabled ? '#' : $href }}"
    @if($disabled) tabindex="-1" aria-disabled="true" @endif
    {{ $attributes->merge(['class' => $linkClasses]) }} 
    {{ $target ? 'target="_blank"' : '' }}
    @if(!$isExternal) wire:navigate @endif wire:navigate>
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
</a>
