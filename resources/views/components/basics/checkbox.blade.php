@props([
    'label' => '',
    'name' => '',
    'value' => '',
    'checked' => false,
    'color' => '#020230', // Default theme color
    'mb' => 4,
])

@php
    // Ensure color is safe and consistent
    $styleColor = $color;
@endphp

<label class="inline-flex items-center space-x-2 cursor-pointer select-none mb-{{ $mb }}">
    <input
        type="checkbox"
        name="{{ str_ends_with($name, '[]') ? $name : $name . '[]' }}"
        value="{{ $value }}"
        {{ $checked ? 'checked' : '' }}
        {{ $attributes->merge([
            'class' => 'form-checkbox h-4 w-4 border-gray-300',
            'style' => "--tw-accent-color: {$styleColor}; accent-color: {$styleColor};"
        ]) }}
    >
    <span class="text-sm text-gray-700">{!! $label !!}</span>
</label>
