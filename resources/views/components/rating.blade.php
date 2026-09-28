@props(['value' => null, 'count' => null])
@if ($value)
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 text-sm']) }}>
        <i class="ph ph-fill ph-star text-amber-500"></i>
        <span class="font-semibold text-gray-900">{{ number_format($value, 1) }}</span>
        @if ($count !== null)<span class="text-gray-500">({{ $count }})</span>@endif
    </span>
@else
    <span class="text-sm text-gray-400">Pas encore d'avis</span>
@endif
