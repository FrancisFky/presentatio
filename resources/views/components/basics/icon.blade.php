@props([
    'name' => 'ph',
    'size' => 'text-base',
    'class' => '',
])

@php
    $iconClass = "ph {$name} {$size} {$class}";
@endphp

<i {{ $attributes->merge(['class' => $iconClass]) }}></i>
