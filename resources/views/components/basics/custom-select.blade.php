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
])

@php
    $id = $id ?? $name;
    $current = $value ?? old($name);
    
    // Find the selected option
    $selectedOption = collect($options)->firstWhere('id', $current);
    $selectedText = $selectedOption ? $selectedOption->name : $placeholder;
    
    // Base select classes - consistent with input components
    $baseSelectClasses = 'relative w-full h-11 rounded-lg border-0 py-2.5 px-4 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 transition-all duration-200 cursor-pointer';
    
    // Add error state classes
    if ($error) {
        $baseSelectClasses .= ' ring-red-500 focus:ring-red-500';
    } else {
        $baseSelectClasses .= ' focus:ring-1 focus:ring-inset focus:ring-brand-600';
    }
    
    // Add disabled state classes
    if ($disabled) {
        $baseSelectClasses .= ' bg-gray-100 text-gray-500 cursor-not-allowed opacity-60 ring-gray-200';
    } else {
        $baseSelectClasses .= ' hover:ring-gray-400';
    }
    
    // Add custom select classes
    $selectClasses = $baseSelectClasses . ' ' . $selectClass;
    
    // Base label classes - matching other components
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

<div class="space-y-2 {{ $class }}" x-data="{
    isOpen: false,
    selectedValue: '{{ $current }}',
    selectedText: '{{ $selectedText }}',
    options: {{ json_encode($options) }},
    
    selectOption(value, text) {
        this.selectedValue = value;
        this.selectedText = text;
        this.isOpen = false;
        console.log('Selected:', value, text);
    },
    
    toggleDropdown() {
        this.isOpen = !this.isOpen;
        console.log('Dropdown toggled:', this.isOpen);
    }
}">
    @if($label)
        <label for="{{ $id }}" class="{{ $labelClasses }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <!-- Hidden input for form submission -->
    <input type="hidden" name="{{ $name }}" :value="selectedValue" />
    
    <!-- Custom Select Button -->
    <div class="relative">
        <button
            type="button"
            @click="toggleDropdown()"
            :class="selectedValue ? 'text-gray-900' : 'text-gray-400'"
            class="{{ $selectClasses }}"
            @if($disabled) disabled @endif
            {{ $attributes->except(['class']) }}
        >
            <span class="block truncate text-left" x-text="selectedText"></span>
            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 transition-transform duration-200" 
                     :class="{ 'rotate-180': isOpen }"
                     xmlns="http://www.w3.org/2000/svg" 
                     viewBox="0 0 20 20" 
                     fill="currentColor">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </span>
        </button>
        
        <!-- Dropdown Options -->
        <div x-show="isOpen"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             @click.away="isOpen = false"
             class="absolute z-50 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none"
             style="display: none;">
            
            
            <!-- Placeholder option -->
            @if($placeholder)
                <div @click="selectOption('', '{{ $placeholder }}')"
                     class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-gray-400 hover:bg-gray-50">
                    <span class="block truncate">{{ $placeholder }}</span>
                    <span x-show="selectedValue === ''" class="absolute inset-y-0 right-0 flex items-center pr-4 text-brand-600">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </div>
            @endif
            
            <!-- Options -->
            @foreach ($options as $option)
                <div @click="selectOption('{{ $option->id }}', '{{ $option->name }}')"
                     class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-gray-900 hover:bg-gray-50">
                    <span class="block truncate">{{ $option->name }}</span>
                    <span x-show="selectedValue === '{{ $option->id }}'" class="absolute inset-y-0 right-0 flex items-center pr-4 text-brand-600">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </div>
            @endforeach
        </div>
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


