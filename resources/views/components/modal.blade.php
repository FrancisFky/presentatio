@props([
    'show' => 'modalOpen',   // Alpine.js property
    'title' => '',           // Modal title
    'description' => '',     // Optional description
    'size' => 'md',          // sm, md, lg, xl, 2xl
])

@php
$widthClasses = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl', 
    '2xl' => 'max-w-2xl',
];
$modalWidth = $widthClasses[$size] ?? $widthClasses['md'];
@endphp

<div
    x-show="{{ $show }}"
    x-transition
    @click.self="{{ $show }} = false"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
    x-cloak
>
    <div
        x-transition
        class="w-full {{ $modalWidth }} max-h-[90vh] bg-white rounded-lg shadow-lg overflow-y-auto"
    >
        <div class="px-6 py-6 border-b border-gray-200">
            @if($title)
                <h2 class="text-2xl font-bold text-gray-800">{{ $title }}</h2>
            @endif
            @if($description)
                <p class="text-sm text-gray-600 mt-1">{{ $description }}</p>
            @endif
        </div>
        <div class="px-6 py-4">
            {{ $slot }}
        </div>
    </div>
</div>
