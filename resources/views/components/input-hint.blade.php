@props(['id' => null])

<p {{ $attributes->merge(['class' => 'mt-1 text-sm text-gray-500']) }} @if($id) id="{{ $id }}" @endif>
    {{ $slot }}
</p> 