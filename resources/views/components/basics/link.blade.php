@props([
    'href' => '#',
    'class' => '',
    'size' => 'xs',
    'button' => false,
    'type' => 'button',
])

@php
    // Check if it's an external URL (starts with http:// or https://)
    $isExternal = !empty($href) && (str_starts_with($href, 'http://') || str_starts_with($href, 'https://'));
@endphp

@if($button)
    <button type="{{ $type }}" class="text-{{ $size }} text-brand-600 font-semibold hover:underline text-left flex items-center height-11 {{ $class }}">
        {{ $slot }}
    </button>
@else
    <a href="{{ $href }}" @if(!$isExternal) wire:navigate @endif class="text-{{ $size }} text-brand-600 font-semibold hover:underline text-left flex items-center height-11 {{ $class }}">
        {{ $slot }}
    </a>
@endif