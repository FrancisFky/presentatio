@props([
    'title' => '',
    'count' => null,
    'subtitle' => '',
    'change' => '',
    'trend' => 'up',
    'color' => 'green',
    'icon' => 'ph-shopping-cart',
    'link' => null,
])

@if ($link)
    <a href="{{ $link }}" {{ $attributes->merge(['class' => 'block']) }}>
        <div class="bg-{{ $color }}-600 rounded p-6 text-white hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-{{ $color }}-100 text-sm">{{ $title }}</p>
                    <p class="text-2xl font-bold">{{ $count }}</p>
                    @if (isset($subtitle))
                        <p class="text-xs text-{{ $color }}-200">{{ $subtitle }}</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="ph {{ $icon }} text-2xl"></i>
                </div>
            </div>
        </div>
    </a>
@else
    <div class="bg-{{ $color }}-600 rounded p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-{{ $color }}-100 text-sm">{{ $title }}</p>
                <p class="text-2xl font-bold">{{ $count }}</p>
                @if (isset($subtitle))
                    <p class="text-xs text-{{ $color }}-200">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
    </div>
@endif
