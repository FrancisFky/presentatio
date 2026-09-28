@props(['service', 'headingLevel' => 'h3'])

<article {{ $attributes->class(['group relative flex flex-col site-card p-6 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl sm:p-7']) }}>
    <span class="flex size-14 items-center justify-center rounded-xl bg-brand-700 text-2xl text-gold-300 shadow-inner transition group-hover:bg-gold-400 group-hover:text-brand-900" aria-hidden="true">
        <i class="ph ph-{{ $service->icon ?: 'file-text' }}"></i>
    </span>
    <{{ $headingLevel }} class="mt-6 font-display text-2xl leading-snug font-semibold text-brand-700">
        <a href="{{ route('site.services.show', $service) }}" class="after:absolute after:inset-0 focus-visible:outline-none group-focus-within:underline decoration-gold-400 underline-offset-4">{{ $service->t('title') }}</a>
    </{{ $headingLevel }}>
    @if ($description = \Illuminate\Support\Str::limit(trim(html_entity_decode(strip_tags((string) $service->t('description')))), 130))
        <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $description }}</p>
    @endif
    <div class="mt-auto flex items-center justify-between gap-3 pt-6 text-sm">
        @if ($time = $service->t('processing_time'))
            <span class="inline-flex items-center gap-1.5 text-slate-500"><i class="ph ph-hourglass-medium text-gold-600" aria-hidden="true"></i>{{ $time }}</span>
        @else
            <span></span>
        @endif
        <span class="inline-flex items-center gap-1 font-semibold text-brand-500" aria-hidden="true">
            {{ __('site.actions.details') }}<i class="ph-bold ph-arrow-right transition group-hover:translate-x-1"></i>
        </span>
    </div>
</article>
