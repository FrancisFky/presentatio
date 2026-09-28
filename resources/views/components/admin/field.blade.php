{{-- Champ simple (texte, date, heure, e-mail, URL, nombre) --}}
@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'help' => null])
@php $error = $errors->first($name); @endphp
<div class="space-y-2">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-900">
        {{ $label }} @if ($required)<span class="text-red-500">*</span>@endif
    </label>
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}" @if ($required) required @endif
        {{ $attributes->merge(['class' => 'block h-11 w-full rounded-lg border-0 px-4 text-sm text-gray-900 shadow-sm ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-brand-500 ' . ($error ? 'ring-red-500' : 'ring-gray-300')]) }}>
    @if ($error)<p class="text-sm text-red-600">{{ $error }}</p>@endif
    @if ($help)<p class="text-sm text-gray-500">{{ $help }}</p>@endif
</div>
