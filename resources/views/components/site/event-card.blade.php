@props(['event', 'headingLevel' => 'h3'])

<article {{ $attributes->class(['group relative flex gap-5 site-card p-5 transition duration-300 hover:shadow-xl sm:p-6']) }}>
    <x-site.date-badge :date="$event->starts_on" />
    <div class="min-w-0 flex-1">
        <{{ $headingLevel }} class="font-display text-xl leading-snug font-semibold text-brand-700 sm:text-2xl">
            <a href="{{ route('site.events.show', $event) }}" class="after:absolute after:inset-0 focus-visible:outline-none group-focus-within:underline decoration-gold-400 underline-offset-4">{{ $event->t('title') }}</a>
        </{{ $headingLevel }}>
        <ul class="mt-3 space-y-1.5 text-sm text-slate-600">
            <li class="flex items-center gap-2">
                <i class="ph ph-clock text-gold-600" aria-hidden="true"></i>
                <span>{{ $event->starts_on->translatedFormat('l j F') }}@if ($event->time()) · {{ $event->time() }}@endif</span>
            </li>
            @if ($venue = $event->t('venue'))
                <li class="flex items-center gap-2">
                    <i class="ph ph-map-pin text-gold-600" aria-hidden="true"></i><span class="truncate">{{ $venue }}</span>
                </li>
            @endif
        </ul>
    </div>
    <i class="ph-bold ph-arrow-up-right self-start text-brand-300 transition group-hover:text-gold-600" aria-hidden="true"></i>
</article>
