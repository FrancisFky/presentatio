@props([
    'name' => '',
    'label' => '',
    'placeholder' => 'Rechercher...',
    'options' => [],
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'displayLabel' => 'name', // New prop for what to display when selected
    'wire:model' => '',
    'required' => false,
    'disabled' => false,
    'mb' => 'mb-4',
    'searchPlaceholder' => 'Rechercher un produit...',
    'minSearchLength' => 2,
    'maxResults' => 10,
    'orientation' => 'auto', // auto | top | bottom
    'class' => '',
])

<div class="{{ $mb }} {{ $class }}">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative" 
         x-data="inputSelect({
            name: '{{ $name }}',
            options: {{ json_encode($options) }},
            optionValue: '{{ $optionValue }}',
            optionLabel: '{{ $optionLabel }}',
            displayLabel: '{{ $displayLabel }}',
            placeholder: '{{ $placeholder }}',
            searchPlaceholder: '{{ $searchPlaceholder }}',
            minSearchLength: {{ $minSearchLength }},
            maxResults: {{ $maxResults }},
            required: {{ $required ? 'true' : 'false' }},
            disabled: {{ $disabled ? 'true' : 'false' }},
            orientation: '{{ $orientation }}',
            initialValue: null
         })" 
         @option-selected="handleOptionSelected($event.detail)"
         @clear-input-select-{{ $name }}.window="clearSelection()">
        <!-- Hidden input for form submission -->
        <input 
            type="hidden" 
            name="{{ $name }}" 
            x-ref="hiddenInput"
            :value="selectedValue"
            {{ $required ? 'required' : '' }}
        />

        <!-- Display selected value or placeholder -->
        <button 
            type="button"
            @click="toggleDropdown()"
            :disabled="disabled"
            class="w-full h-11 bg-white rounded-lg border-0 py-2.5 px-4 text-left cursor-pointer shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-brand-600 transition-all duration-200 disabled:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-60 disabled:ring-gray-200"
            :class="{ 'ring-brand-600': isOpen }"
        >
            <span x-show="!selectedOption" class="text-gray-500">{{ $placeholder }}</span>
            <span x-show="selectedOption" class="text-gray-900" x-text="selectedOption"></span>
            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </span>
        </button>

        <!-- Dropdown -->
        <div 
            x-show="isOpen" 
            @click.away="closeDropdown()"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-hidden left-0"
            :class="currentOrientation === 'top' ? 'bottom-full mb-1' : 'top-full mt-1 border-brand-600'"
        >
            <!-- Search input -->
            <div class="p-2 border-b border-gray-200">
                <input 
                    type="text" 
                    x-ref="searchInput"
                    x-model="searchQuery"
                    @input="filterOptions()"
                    @keydown.escape="closeDropdown()"
                    @keydown.enter.prevent="selectFirstOption()"
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full px-3 py-2 border-0 rounded-lg text-sm shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-brand-600 transition-all duration-200"
                />
            </div>

            <!-- Results -->
            <div class="max-h-48 overflow-y-auto">
                <template x-if="filteredOptions.length === 0 && searchQuery.length >= minSearchLength">
                    <div class="px-3 py-2 text-sm text-gray-500 text-center">
                        Aucun résultat trouvé
                    </div>
                </template>
                
                <template x-if="searchQuery.length < minSearchLength">
                    <div class="px-3 py-2 text-sm text-gray-500 text-center">
                        Tapez au moins {{ $minSearchLength }} caractères pour rechercher
                    </div>
                </template>

                <template x-for="(option, index) in filteredOptions" :key="option[optionValue]">
                    <div 
                        @click="selectOption(option)"
                        @mouseenter="highlightedIndex = index"
                        class="px-3 py-2 text-sm cursor-pointer hover:bg-brand-50"
                        :class="{ 'bg-brand-100': highlightedIndex === index }"
                    >
                        <div class="font-medium text-gray-900" x-text="option[optionLabel]"></div>
                        <template x-if="option.category">
                            <div class="text-xs text-gray-500" x-text="option.category"></div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<script>
function inputSelect(config) {
    return {
        name: config.name,
        options: config.options || [],
        optionValue: config.optionValue,
        optionLabel: config.optionLabel,
        displayLabel: config.displayLabel || config.optionLabel, // Use displayLabel if provided, fallback to optionLabel
        placeholder: config.placeholder,
        searchPlaceholder: config.searchPlaceholder,
        minSearchLength: config.minSearchLength,
        maxResults: config.maxResults,
        required: config.required,
        disabled: config.disabled,
        orientation: config.orientation || 'auto',
        currentOrientation: 'bottom',
        initialValue: config.initialValue,
        
        isOpen: false,
        searchQuery: '',
        selectedValue: '',
        selectedOption: '',
        filteredOptions: [],
        highlightedIndex: -1,

        clearSelection() {
            this.selectedValue = '';
            this.selectedOption = '';
            try {
                if (this.$wire) {
                    this.$wire.set(this.name, null);
                }
            } catch (e) {}
        },

        handleOptionSelected(detail) {
            this.selectedValue = detail.value;
            // Use displayLabel for what's shown in the input
            this.selectedOption = detail.option[this.displayLabel];
            
            // Try to update Livewire if available
            try {
                if (this.$wire) {
                    this.$wire.set(this.name, this.selectedValue);
                }
            } catch (e) {
                // Livewire not available, continue without error
            }
        },

        init() {
            this.filteredOptions = this.options.slice(0, this.maxResults);
        },

        setInitialValue(value) {
            const initialValue = value || this.initialValue;
            console.log('setInitialValue called with:', initialValue, 'Options:', this.options);
            
            // Find the option that matches the initial value
            const option = this.options.find(opt => opt[this.optionValue] == initialValue);
            if (option) {
                this.selectedValue = option[this.optionValue];
                this.selectedOption = option[this.displayLabel];
                console.log('Successfully set:', this.selectedValue, this.selectedOption);
            } else {
                console.log('No option found for value:', initialValue);
            }
        },

        toggleDropdown() {
            if (this.disabled) return;
            
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.$refs.searchInput.focus();
                });
                this.evaluateOrientation();
            }
        },

        closeDropdown() {
            this.isOpen = false;
            this.searchQuery = '';
            this.highlightedIndex = -1;
            this.filterOptions();
        },

        filterOptions() {
            if (this.searchQuery.length < this.minSearchLength) {
                this.filteredOptions = this.options.slice(0, this.maxResults);
                return;
            }

            const query = this.searchQuery.toLowerCase();
            this.filteredOptions = this.options
                .filter(option => {
                    const label = String(option[this.optionLabel]).toLowerCase();
                    const category = option.category ? String(option.category).toLowerCase() : '';
                    const genericName = option.generic_name ? String(option.generic_name).toLowerCase() : '';
                    
                    return label.includes(query) || 
                           category.includes(query) || 
                           genericName.includes(query);
                })
                .slice(0, this.maxResults);
        },

        selectOption(option) {
            this.selectedValue = option[this.optionValue];
            // Use displayLabel for what's shown in the input
            this.selectedOption = option[this.displayLabel];
            this.closeDropdown();
            
            // Trigger custom event for parent components
            this.$dispatch('option-selected', {
                value: this.selectedValue,
                option: option
            });
        },

        selectFirstOption() {
            if (this.filteredOptions.length > 0) {
                this.selectOption(this.filteredOptions[0]);
            }
        },

        evaluateOrientation() {
            if (this.orientation === 'top') {
                this.currentOrientation = 'top';
                return;
            }

            if (this.orientation === 'bottom') {
                this.currentOrientation = 'bottom';
                return;
            }

            // auto: compute available space
            try {
                const trigger = this.$el.querySelector('button[type="button"]');
                const rect = trigger.getBoundingClientRect();
                const viewportHeight = window.innerHeight || document.documentElement.clientHeight;

                const spaceBelow = viewportHeight - rect.bottom;
                const spaceAbove = rect.top;

                // Approximate dropdown height: 240px (max-h-60)
                const dropdownHeight = 240;
                this.currentOrientation = spaceBelow >= dropdownHeight || spaceBelow >= spaceAbove ? 'bottom' : 'top';
            } catch (e) {
                this.currentOrientation = 'bottom';
            }
        }
    };
}
</script>
