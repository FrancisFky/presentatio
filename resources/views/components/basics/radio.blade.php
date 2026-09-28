@props([
  'name',
  'label' => '',
  'checked' => false,
])

@php
  $initialValue = $checked ? '1' : '0';
@endphp

<div x-data="{ toggle: '{{ $initialValue }}' }" class="flex items-center justify-between">
  @if($label)
    <span class="text-gray-700 font-medium">{{ $label }}</span>
  @endif

  <template x-if="toggle === '1'">
    <label @click="toggle = '0'" class="flex items-center cursor-pointer">
      <input type="radio" name="{{ $name }}" value="1" x-model="toggle" class="sr-only peer" />
      <div class="w-11 h-6 bg-emerald-600 rounded-full relative transition-colors">
        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform transform translate-x-5"></div>
      </div>
    </label>
  </template>

  <template x-if="toggle === '0'">
    <label @click="toggle = '1'" class="flex items-center cursor-pointer">
      <input type="radio" name="{{ $name }}" value="0" x-model="toggle" class="sr-only peer" />
      <div class="w-11 h-6 bg-red-500 rounded-full relative transition-colors">
        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform transform"></div>
      </div>
    </label>
  </template>
</div>
