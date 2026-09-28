@props([
    'icon' => null,        // e.g. 'ph ph-pencil'
    'href' => null,        // if set, renders an <a>
    'variant' => 'default', // 'default' | 'danger' | 'success'
    'target' => null,
])

@php
    $base = 'flex w-full items-center gap-2 px-3 py-2 text-sm rounded-md';
    $states = 'hover:bg-gray-50 focus:bg-gray-50 focus:outline-none';
    $text = $variant === 'danger' ? 'text-red-600 hover:text-red-700' : ($variant === 'success' ? 'text-green-600 hover:text-green-700' : 'text-gray-700 hover:text-gray-900');
    $classes = "$base $states $text";
    
    // Check if it's an external URL (starts with http:// or https://)
    $isExternal = !empty($href) && (str_starts_with($href, 'http://') || str_starts_with($href, 'https://'));
@endphp
        
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes, 'role' => 'menuitem', 'tabindex' => '-1']) }} {{ $target ? 'target="_blank"' : '' }} @if(!$isExternal) wire:navigate @endif>
        @if ($icon)<i class="{{ $icon }} text-base"></i>@endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button type="button" {{ $attributes->merge(['class' => $classes, 'role' => 'menuitem']) }}>
        @if ($icon)<i class="{{ $icon }} text-base"></i>@endif
        <span>{{ $slot }}</span>
    </button>
@endif
