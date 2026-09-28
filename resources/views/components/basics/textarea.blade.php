@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => '',
    'placeholder' => '',
    'error' => null,
    'help' => null,
    'rows' => 4,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'class' => '',
    'textareaClass' => '',
    'labelClass' => '',
    'errorClass' => '',
    'helpClass' => '',
])

@php
    $id = $id ?? $name;
    $textareaValue = old($name, $value);
    
    // Base textarea classes - consistent with input/select components
    $baseTextareaClasses = 'block w-full rounded-lg border-0 py-2.5 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 placeholder:text-sm focus:ring-1 focus:ring-inset focus:ring-brand-600 transition-all duration-200 resize-y';
    
    // Add error state classes - same ring thickness
    if ($error) {
        $baseTextareaClasses .= ' ring-red-500 focus:ring-red-500';
    }
    
    // Add disabled state classes
    if ($disabled) {
        $baseTextareaClasses .= ' bg-gray-100 text-gray-500 cursor-not-allowed opacity-60 ring-gray-200';
        $baseTextareaClasses = str_replace(' focus:ring-1 focus:ring-inset focus:ring-brand-600', '', $baseTextareaClasses);
    }
    
    // Add custom textarea classes
    $textareaClasses = $baseTextareaClasses . ' ' . $textareaClass;
    
    // Base label classes - matching input/select components
    $baseLabelClasses = 'block text-sm font-medium leading-6 text-gray-900';
    
    // Add disabled label styling
    if ($disabled) {
        $baseLabelClasses .= ' text-gray-500';
    }
    
    $labelClasses = $baseLabelClasses . ' ' . $labelClass;
    
    // Base error classes - matching input/select components
    $baseErrorClasses = 'mt-1 text-sm text-red-600';
    $errorClasses = $baseErrorClasses . ' ' . $errorClass;
    
    // Base help classes - matching input/select components
    $baseHelpClasses = 'mt-1 text-sm text-gray-500';
    $helpClasses = $baseHelpClasses . ' ' . $helpClass;
@endphp

<div class="space-y-2 {{ $class }}">
    @if($label)
        <label for="{{ $id }}" class="{{ $labelClasses }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $id }}"
        placeholder="{{ $placeholder }}"
        rows="{{ $rows }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        {{ $attributes->merge(['class' => $textareaClasses]) }}
    >{{ $attributes->has('wire:model') ? '' : $textareaValue }}</textarea>

    @if($error)
        <div class="{{ $errorClasses }}">
            {{ $error }}
        </div>
    @endif
    
    @if($help)
        <div class="{{ $helpClasses }}">
            {{ $help }}
        </div>
    @endif
</div>
