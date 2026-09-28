@props([
  'name',
  'label' => '',
  'checked' => false,
])

<div x-data="{ toggle: {{ $checked ? 'true' : 'false' }} }" class="inline-flex items-center cursor-pointer select-none">
  <!-- Hidden input to submit value -->
  <input type="checkbox"
         name="{{ $name }}"
         class="sr-only"
         :checked="toggle"
         x-model="toggle"
         value="1"
  />

  <!-- Toggle Track -->
  <div class="w-11 h-6 bg-gray-300 rounded-full relative transition-colors duration-300"
       :class="{ 'bg-emerald-600': toggle }">
    <!-- Toggle Knob -->
    <div class="w-4 h-4 bg-white rounded-full shadow absolute top-1 left-1 
                transition-transform duration-300 transform"
         :class="{ 'translate-x-5': toggle }">
    </div>
  </div>

  @if($label)
    <span class="ml-3 text-gray-700 font-medium" x-text="toggle ? '{{ $label }}: Oui' : '{{ $label }}: Non'"></span>
  @endif
</div>
