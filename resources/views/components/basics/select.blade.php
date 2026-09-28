@props([
    'name',
    'id' => null,
    'label' => null,
    'options' => [],
    'placeholder' => 'Select an option',
    'value' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'help' => null,
    'class' => '',
    'selectClass' => '',
    'labelClass' => '',
    'errorClass' => '',
    'helpClass' => '',
    'mb' => '',
    'optionValue' => 'id',
    'optionLabel' => 'name',
])

@php
    $id = $id ?? $name;
    $current = $value ?? old($name);
    
    // Base select classes with standardized height (44px) - consistent ring thickness
    $baseSelectClasses = 'block w-full h-11 rounded-lg border-0 py-2.5 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 placeholder:text-sm focus:ring-1 focus:ring-inset focus:ring-brand-600 transition-all duration-200';
    
    // Add error state classes - same ring thickness
    if ($error) {
        $baseSelectClasses .= ' ring-red-500 focus:ring-red-500';
    }
    
    // Add disabled state classes
    if ($disabled) {
        $baseSelectClasses .= ' bg-gray-100 text-gray-500 cursor-not-allowed opacity-60 ring-gray-200';
        $baseSelectClasses = str_replace(' focus:ring-1 focus:ring-inset focus:ring-brand-600', '', $baseSelectClasses);
    }
    
    // Add custom select classes
    $selectClasses = $baseSelectClasses . ' ' . $selectClass;
    
    // Base label classes - matching input component
    $baseLabelClasses = 'block text-sm font-medium leading-6 text-gray-900';
    
    // Add disabled label styling
    if ($disabled) {
        $baseLabelClasses .= ' text-gray-500';
    }
    
    $labelClasses = $baseLabelClasses . ' ' . $labelClass;
    
    // Base error classes - matching input component
    $baseErrorClasses = 'mt-1 text-sm text-red-600';
    $errorClasses = $baseErrorClasses . ' ' . $errorClass;
    
    // Base help classes - matching input component
    $baseHelpClasses = 'mt-1 text-sm text-gray-500';
    $helpClasses = $baseHelpClasses . ' ' . $helpClass;

    // Helper to read values from array or object
    $read = function ($item, $key) {
        if (is_array($item)) {
            return $item[$key] ?? null;
        }
        if (is_object($item)) {
            return $item->{$key} ?? null;
        }
        return null;
    };
@endphp

<div class="space-y-2 {{ $class }} {{ $mb }}">
    @if($label)
        <label for="{{ $id }}" class="{{ $labelClasses }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <select 
        name="{{ $name }}" 
        id="{{ $id }}" 
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge(['class' => $selectClasses]) }}
    >
        <option value="">{{ $placeholder }}</option>
        
        @foreach ($options as $option)
            @php
                $ov = $read($option, $optionValue);
                $ol = $read($option, $optionLabel);
            @endphp
            <option value="{{ $ov }}" @selected($current == $ov)>
                {{ $ol }}
            </option>
        @endforeach
    </select>
    
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