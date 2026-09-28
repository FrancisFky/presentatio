@props([
    'name',
    'id' => null,
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'options' => [],
    'required' => false,
    'disabled' => false,
    'error' => null,
    'help' => null,
    'class' => '',
    'selectClass' => '',
    'labelClass' => '',
    'errorClass' => '',
    'helpClass' => '',
    'optionsLabel' => 'name',
    'optionsValue' => 'id',
])

@php
    $id = $id ?? $name;
    $value = $value ?? old($name);
    
    // Base select classes with standardized height (44px)
    $baseSelectClasses = 'block w-full h-11 rounded-lg border-0 py-2.5 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-brand-600 transition-all duration-200 text-sm';
    
    // Add error state classes
    if ($error) {
        $baseSelectClasses .= ' ring-red-500 focus:ring-red-500';
    }
    
    // Add disabled state classes
    if ($disabled) {
        $baseSelectClasses .= ' bg-gray-100 text-gray-500 cursor-not-allowed opacity-60 ring-gray-200';
        $baseSelectClasses = str_replace(' focus:ring-2 focus:ring-inset focus:ring-brand-600', '', $baseSelectClasses);
    }
    
    // Add custom select classes
    $selectClasses = $baseSelectClasses . ' ' . $selectClass;
    
    // Base label classes
    $baseLabelClasses = 'block text-sm font-medium leading-6 text-gray-900';
    
    // Add disabled label styling
    if ($disabled) {
        $baseLabelClasses .= ' text-gray-500';
    }
    
    $labelClasses = $baseLabelClasses . ' ' . $labelClass;
    
    // Base error classes
    $baseErrorClasses = 'mt-1 text-sm text-red-600';
    $errorClasses = $baseErrorClasses . ' ' . $errorClass;
    
    // Base help classes
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
    
    <div class="relative">
        <select 
            name="{{ $name }}"
            id="{{ $id }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->merge(['class' => $selectClasses]) }}
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            
            @foreach($options as $option)
                @if(is_object($option))
                    <option value="{{ $option->$optionsValue }}" {{ $value == $option->$optionsValue ? 'selected' : '' }}>
                        {{ $option->$optionsLabel }}
                    </option>
                @elseif(is_array($option))
                    <option value="{{ $option[$optionsValue] ?? $option['value'] }}" {{ $value == ($option[$optionsValue] ?? $option['value']) ? 'selected' : '' }}>
                        {{ $option[$optionsLabel] ?? $option['label'] }}
                    </option>
                @else
                    <option value="{{ $option }}" {{ $value == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endif
            @endforeach
        </select>
        
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