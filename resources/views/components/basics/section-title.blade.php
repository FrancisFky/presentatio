@props([
    'title' => '',
    'titleSize' => 'lg', // xs, sm, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl
    'titleVariant' => 'black', // black, primary, success, danger, warning, info
    'titleWeight' => 'bold', // bold, semibold
    'titleCenter' => false, // true, false
    'subtitle' => '',
    'subtitleSize' => 'sm', // xs, sm, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl
    'subtitleVariant' => 'gray-600', // black, primary, success, danger, warning, info
    'subtitleWeight' => 'normal', // normal, bold, semibold
    'subtitleCenter' => false, // true, false
    'class' => '',
])

@php
$variantTitleClasses = match($titleVariant) {
    'black' => 'text-black',
    'primary' => 'text-brand-600',
    'success' => 'text-green-600',
    'danger' => 'text-red-600',
    'warning' => 'text-yellow-600',
    'info' => 'text-blue-600',
    'gray-600' => 'text-gray-600',
    default => 'text-gray-600',
};

$variantSubtitleClasses = match($subtitleVariant) {
    'black' => 'text-black',
    'primary' => 'text-brand-600',
    'success' => 'text-green-600',
    'danger' => 'text-red-600',
    'warning' => 'text-yellow-600',
    'info' => 'text-blue-600',
    default => 'text-gray-600',
};
@endphp

<div class="{{ $class }}">
    <h2 class="text-{{ $titleSize }} font-{{ $titleWeight }} {{ $variantTitleClasses }} {{ $titleCenter ? 'text-center' : '' }}">{{ $title }}</h2>
    @if($subtitle)
    <p class="text-{{ $subtitleSize }} font-{{ $subtitleWeight }} {{ $variantSubtitleClasses }} {{ $subtitleCenter ? 'text-center' : '' }}">{{ $subtitle }}</p>
    @endif
</div>