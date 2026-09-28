{{-- Bascule Français / English d'un formulaire : le parent porte x-data="{ lang: 'fr' }" --}}
@props(['sticky' => true])
<div {{ $attributes->merge(['class' => ($sticky ? 'sticky top-16 z-20 ' : '') . 'flex items-center gap-2 rounded-lg border border-gray-200 bg-white/95 p-1.5 shadow-sm backdrop-blur w-fit']) }}>
    @if ($sticky)<span class="px-2 text-xs font-medium uppercase tracking-wider text-gray-400">Langue</span>@endif
    @foreach (\App\Support\Locales::LABELS as $code => $label)
        <button type="button" @click="lang = '{{ $code }}'"
            :class="lang === '{{ $code }}' ? 'bg-brand-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
            class="whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium transition">
            <span class="uppercase">{{ $code }}</span>@if ($sticky) · {{ $label }}@endif
        </button>
    @endforeach
</div>
