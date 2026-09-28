{{-- Pavé date façon carton d'invitation : jour en grand, mois en capitales --}}
@props(['date', 'dark' => false])

<time datetime="{{ $date->toDateString() }}" {{ $attributes->class([
    'flex w-18 shrink-0 flex-col items-center justify-center rounded-lg py-3 text-center',
    'bg-brand-700 text-white' => ! $dark,
    'bg-white/10 text-white ring-1 ring-white/15' => $dark,
]) }}>
    <span class="text-[11px] font-semibold tracking-[0.18em] text-gold-300 uppercase">{{ $date->translatedFormat('M') }}</span>
    <span class="font-display text-4xl leading-none font-semibold">{{ $date->format('d') }}</span>
    <span class="mt-1 text-[11px] text-brand-200">{{ $date->format('Y') }}</span>
</time>
