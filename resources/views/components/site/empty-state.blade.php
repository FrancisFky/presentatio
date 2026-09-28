{{-- Rubrique encore vide : un message aimable plutôt qu'une page blanche --}}
@props(['icon' => 'ph-tray', 'title', 'text' => null])

<div {{ $attributes->class(['mx-auto flex max-w-lg flex-col items-center rounded-2xl border border-dashed border-brand-200 bg-white/60 px-6 py-14 text-center']) }}>
    <span class="flex size-16 items-center justify-center rounded-full bg-paper text-3xl text-gold-600 ring-1 ring-gold-200" aria-hidden="true">
        <i class="ph {{ $icon }}"></i>
    </span>
    <h2 class="mt-5 font-display text-2xl font-semibold text-brand-700">{{ $title }}</h2>
    @if ($text)
        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $text }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-6">{{ $slot }}</div>
    @endif
</div>
