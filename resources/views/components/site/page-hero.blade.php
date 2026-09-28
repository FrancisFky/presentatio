{{--
    Bandeau d'en-tête des pages intérieures : photo voilée de bleu (ou fond uni),
    fil d'Ariane, titre. $crumbs : [libellé => url] entre « Accueil » et la page courante.
--}}
@props(['title', 'eyebrow' => null, 'intro' => null, 'image' => null, 'crumbs' => []])

<section {{ $attributes->class(['relative isolate overflow-hidden bg-brand-700']) }}>
    @if ($image)
        <img src="{{ $image }}" alt="" class="absolute inset-0 -z-20 size-full object-cover" fetchpriority="high">
        <div class="absolute inset-0 -z-10 bg-gradient-to-r from-brand-900/95 via-brand-900/80 to-brand-700/55" aria-hidden="true"></div>
    @else
        <div class="site-guilloche absolute inset-0 -z-10" aria-hidden="true"></div>
    @endif
    <div class="absolute inset-x-0 bottom-0 -z-10 h-px bg-gradient-to-r from-transparent via-gold-400/70 to-transparent" aria-hidden="true"></div>

    <div class="site-container py-14 sm:py-20 lg:py-24">
        <nav aria-label="{{ __('site.a11y.breadcrumb') }}">
            <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-[13px] text-brand-200">
                <li><a href="{{ route('site.home') }}" class="hover:text-white">{{ __('site.nav.home') }}</a></li>
                @foreach ($crumbs as $label => $url)
                    <li aria-hidden="true"><i class="ph ph-caret-right text-[10px] text-gold-400"></i></li>
                    <li><a href="{{ $url }}" class="hover:text-white">{{ $label }}</a></li>
                @endforeach
                <li aria-hidden="true"><i class="ph ph-caret-right text-[10px] text-gold-400"></i></li>
                <li aria-current="page" class="line-clamp-1 text-white">{{ $title }}</li>
            </ol>
        </nav>
        @if ($eyebrow)
            <p class="site-eyebrow site-eyebrow-light mt-8">{{ $eyebrow }}</p>
        @endif
        <h1 @class(['max-w-4xl font-display text-4xl leading-[1.08] font-semibold text-balance text-white sm:text-5xl lg:text-6xl', 'mt-3' => $eyebrow, 'mt-8' => ! $eyebrow])>{{ $title }}</h1>
        @if ($intro)
            <p class="mt-5 max-w-2xl text-base leading-relaxed text-brand-100 sm:text-lg">{{ $intro }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
