@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'required' => false,
    'error' => null,
    'help' => null,
    'class' => '',
    'labelClass' => '',
    'errorClass' => '',
    'helpClass' => '',
    'checkboxClass' => '',
])

@php
    $id = $id ?? $name . '_' . $value;
    
    // Check if the checkbox should be checked
    $isChecked = $checked;
    if (!$isChecked) {
        $oldValue = old($name);
        if (is_array($oldValue)) {
            $isChecked = in_array($value, $oldValue);
        } else {
            $isChecked = $oldValue == $value;
        }
    }
    
    // Base checkbox classes
    $baseCheckboxClasses = 'h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-600 transition-colors duration-200';
    
    // Add error state classes
    if ($error) {
        $baseCheckboxClasses .= ' border-red-500 focus:ring-red-500';
    }
    
    // Add custom checkbox classes
    $checkboxClasses = $baseCheckboxClasses . ' ' . $checkboxClass;
    
    // Base label classes
    $baseLabelClasses = 'ml-2 block text-sm text-gray-900';
    $labelClasses = $baseLabelClasses . ' ' . $labelClass;
    
    // Base error classes
    $baseErrorClasses = 'mt-1 text-sm text-red-600';
    $errorClasses = $baseErrorClasses . ' ' . $errorClass;
    
    // Base help classes
    $baseHelpClasses = 'mt-1 text-sm text-gray-500';
    $helpClasses = $baseHelpClasses . ' ' . $helpClass;
@endphp

<div class="space-y-1 {{ $class }}">
    <div class="flex items-center">
        <input 
            type="checkbox"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $value }}"
            @if($isChecked) checked @endif
            @if($disabled) disabled @endif
            @if($required) required @endif
            {{ $attributes->merge(['class' => $checkboxClasses]) }}
        >
        
        @if($label)
            <label for="{{ $id }}" class="{{ $labelClasses }}">
                {{ $label }}
                @if($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        @endif
    </div>
    
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