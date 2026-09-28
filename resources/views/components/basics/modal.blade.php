@props([
    'show' => 'modalOpen',   // Alpine.js property
    'title' => '',           // Modal title
    'description' => '',     // Optional description
    'size' => 'md',          // sm, md, lg, xl, 2xl
    'canClose' => true,
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
    @if($canClose)
        @click.self="{{ $show }} = false"
    @endif
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
    x-cloak
>
    <div
        x-transition
        class="w-full {{ $modalWidth }} max-h-[90vh] bg-white rounded-lg shadow-lg overflow-y-auto"
    >
        <div class="px-6 py-6 border-b border-gray-200 flex justify-between items-start">
            <div>
                @if($title)
                    <h2 class="text-2xl font-bold text-gray-800">{{ $title }}</h2>
                @endif
                @if($description)
                    <p class="text-sm text-gray-600 mt-1">{{ $description }}</p>
                @endif
            </div>
            @if($canClose)
                <button type="button" @click="{{ $show }} = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="ph ph-x text-xl"></i>
                </button>
            @endif
        </div>
        <div class="px-6 py-8">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        @isset($footer)
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
