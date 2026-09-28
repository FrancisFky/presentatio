@props([
    'align' => 'right',        // 'right' | 'left'
    'width' => 'w-56',         // any Tailwind width class
    'closeOnClick' => true,    // close menu when clicking an item
    'z' => 'z-50',             // z-index utility
    'disabled' => false,       // disable opening
])

@php
    $alignClass = $align === 'left'
        ? 'origin-top-left left-0'
        : 'origin-top-right right-0';
@endphp

<div
    x-data="{ open: false, closeOnClick: {{ $closeOnClick ? 'true' : 'false' }} }"
    @keydown.escape.window="open = false"
    @click.outside="open = false"
    class="relative inline-block text-left"
>
    {{-- Trigger --}}
    <div
        @click="if (!{{ $disabled ? 'true' : 'false' }}) open = !open"
        @keydown.enter.prevent="if (!{{ $disabled ? 'true' : 'false' }}) open = !open"
        @keydown.space.prevent="if (!{{ $disabled ? 'true' : 'false' }}) open = !open"
        role="button"
        :aria-expanded="open"
        aria-haspopup="true"
    >
        {{ $trigger ?? '' }}
    </div>

    {{-- Panel --}}
    <div
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute mt-2 {{ $alignClass }} {{ $z }} {{ $width }}"
    >
        <div
            class="rounded-md bg-white border border-gray-200 shadow-lg p-1"
            @click="if (closeOnClick) open = false"
            role="menu"
            tabindex="-1"
        >
            {{ $content ?? '' }}
        </div>
    </div>
</div>
