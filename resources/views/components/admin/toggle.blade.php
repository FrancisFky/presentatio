{{-- Case à cocher envoyée même décochée (0), lue avec $request->boolean() --}}
@props(['name', 'label', 'checked' => false, 'help' => null])
<div>
    <input type="hidden" name="{{ $name }}" value="0">
    <label class="inline-flex cursor-pointer items-start gap-3">
        <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $checked))
            class="mt-0.5 h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
        <span>
            <span class="block text-sm font-medium text-gray-900">{{ $label }}</span>
            @if ($help)<span class="block text-sm text-gray-500">{{ $help }}</span>@endif
        </span>
    </label>
</div>
