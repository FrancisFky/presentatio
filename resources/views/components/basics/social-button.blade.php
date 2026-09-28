@props([
    'url' => null,
    'icon' => null,
    'width' => 'w-10',
    'height' => 'h-10',
    'color' => 'text-black',
    'hoverColor' => 'hover:bg-[#E6F4EA]',
    'bgColor' => 'bg-[#FFFFFF]',
    'iconColor' => 'text-black',
])

<a href="{{ $url }}" target="_blank"
    class="{{ $bgColor }} {{ $width }} {{ $height }} rounded-full inline-flex items-center justify-center transition-colors duration-300 {{ $hoverColor }}">
    <i class="ph ph-{{ $icon }} text-lg {{ $color }} {{ $iconColor }}"></i>
</a>