@props(['news', 'headingLevel' => 'h3'])
@use('App\Support\Site')

<article {{ $attributes->class(['group relative flex flex-col overflow-hidden site-card transition duration-300 hover:-translate-y-0.5 hover:shadow-xl']) }}>
    <div class="relative aspect-[16/10] overflow-hidden bg-brand-100">
        <img src="{{ Site::media($news->image_path, Site::DEFAULT_HERO) }}" alt="" loading="lazy"
             class="size-full object-cover transition duration-700 motion-safe:group-hover:scale-105">
        @if ($news->category)
            <span class="absolute top-4 left-4 rounded-full bg-white/95 px-3 py-1 text-[11px] font-semibold tracking-wider text-brand-600 uppercase shadow-sm">{{ $news->category->t('name') }}</span>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-6">
        <time datetime="{{ $news->published_on->toDateString() }}" class="flex items-center gap-2 text-xs font-medium tracking-wide text-gold-700 uppercase">
            <i class="ph ph-calendar-blank" aria-hidden="true"></i>{{ $news->published_on->translatedFormat('j F Y') }}
        </time>
        <{{ $headingLevel }} class="mt-3 font-display text-2xl leading-snug font-semibold text-brand-700">
            <a href="{{ route('site.news.show', $news) }}" class="after:absolute after:inset-0 focus-visible:outline-none group-focus-within:underline decoration-gold-400 underline-offset-4">
                {{ $news->t('title') }}
            </a>
        </{{ $headingLevel }}>
        @if ($excerpt = $news->t('excerpt') ?: \Illuminate\Support\Str::limit(strip_tags((string) $news->t('body')), 150))
            <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-slate-600">{{ $excerpt }}</p>
        @endif
        <span class="mt-auto inline-flex items-center gap-1.5 pt-5 text-sm font-semibold text-brand-500" aria-hidden="true">
            {{ __('site.actions.read_more') }}<i class="ph-bold ph-arrow-right transition group-hover:translate-x-1"></i>
        </span>
    </div>
</article>
