{{-- Liste déroulante : options ['valeur' => 'Libellé'] --}}
@props(['name', 'label', 'options' => [], 'value' => null, 'required' => false, 'placeholder' => null, 'help' => null])
@php $selected = (string) old($name, $value); $error = $errors->first($name); @endphp
<div class="space-y-2">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-900">
        {{ $label }} @if ($required)<span class="text-red-500">*</span>@endif
    </label>
    <select name="{{ $name }}" id="{{ $name }}" @if ($required) required @endif
        {{ $attributes->merge(['class' => 'block h-11 w-full rounded-lg border-0 px-4 text-sm text-gray-900 shadow-sm ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-brand-500 ' . ($error ? 'ring-red-500' : 'ring-gray-300')]) }}>
        @if ($placeholder !== null)<option value="">{{ $placeholder }}</option>@endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected($selected === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @if ($error)<p class="text-sm text-red-600">{{ $error }}</p>@endif
    @if ($help)<p class="text-sm text-gray-500">{{ $help }}</p>@endif
</div>
