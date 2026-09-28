{{-- Pastilles FR / EN : grisée si la traduction manque --}}
@props(['model', 'field' => 'title'])
<span class="inline-flex gap-1">
    @foreach (\App\Support\Locales::SUPPORTED as $locale)
        @php $ok = filled($model->{$field . '_' . $locale}); @endphp
        <span title="{{ $ok ? 'Traduit' : 'Traduction manquante' }}"
            class="rounded px-1.5 py-0.5 text-[10px] font-semibold uppercase {{ $ok ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-600 line-through' }}">{{ $locale }}</span>
    @endforeach
</span>
