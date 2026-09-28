@props(['status', 'labels' => []])
@php
    $label = $labels[$status] ?? ucfirst(str_replace('_', ' ', (string) $status));
    $color = match ($status) {
        'active', 'published', 'confirmed', 'done' => 'bg-green-100 text-green-800',
        'pending', 'unread', 'rescheduled', 'scheduled' => 'bg-amber-100 text-amber-800',
        'read', 'draft' => 'bg-blue-100 text-blue-800',
        'declined', 'expired', 'urgent' => 'bg-red-100 text-red-800',
        'important' => 'bg-gold-100 text-gold-700',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$color}"]) }}>{{ $label }}</span>
