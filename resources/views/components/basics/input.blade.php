@props([
    'type' => 'text',
    'name',
    'id' => null,
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'autocomplete' => null,
    'error' => null,
    'help' => null,
    'showPasswordToggle' => false,
    'class' => '',
    'inputClass' => '',
    'labelClass' => '',
    'errorClass' => '',
    'helpClass' => '',
])

@php
    $id = $id ?? $name;
    $value = $value ?? old($name);
    
    // Base input classes with standardized height (44px) - consistent ring thickness
    $baseInputClasses = 'block w-full h-11 rounded-lg border-0 py-2.5 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 placeholder:text-sm focus:ring-1 focus:ring-inset focus:ring-brand-600 transition-all duration-200';
    
    // Add error state classes - same ring thickness
    if ($error) {
        $baseInputClasses .= ' ring-red-500 focus:ring-red-500';
    }
    
    // Add disabled state classes
    if ($disabled) {
        $baseInputClasses .= ' bg-gray-100 text-gray-500 cursor-not-allowed opacity-60 ring-gray-200';
        $baseInputClasses = str_replace(' focus:ring-1 focus:ring-inset focus:ring-brand-600', '', $baseInputClasses);
    }
    
    // Add custom input classes
    $inputClasses = $baseInputClasses . ' ' . $inputClass;
    
    // Add padding for password toggle
    if ($showPasswordToggle && $type === 'password') {
        $inputClasses .= ' pr-12';
    }
    
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
    
    <div class="relative" @if($showPasswordToggle && $type === 'password') x-data="{ showPassword: false }" @endif>
        <input 
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            autocomplete="{{ $autocomplete }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($showPasswordToggle && $type === 'password') x-bind:type="showPassword ? 'text' : 'password'" @endif
            {{ $attributes->merge(['class' => $inputClasses]) }}
        >
        
        @if($showPasswordToggle && $type === 'password')
            <button 
                type="button" 
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
            >
                <!-- Eye icon (show when password is hidden) -->
                <svg x-show="!showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <!-- Eye slash icon (show when password is visible) -->
                <svg x-show="showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                </svg>
            </button>
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
