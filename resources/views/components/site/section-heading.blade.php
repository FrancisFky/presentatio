{{-- Titre de section : petit libellé doré, grand titre à empattements, chapô facultatif --}}
@props(['eyebrow' => null, 'title', 'intro' => null, 'center' => false, 'dark' => false, 'link' => null, 'linkLabel' => null, 'id' => null])

<div {{ $attributes->class(['flex flex-col gap-6', 'items-center text-center' => $center, 'sm:flex-row sm:items-end sm:justify-between' => ! $center && $link]) }}>
    <div @class(['max-w-2xl', 'mx-auto' => $center])>
        @if ($eyebrow)
            <p @class(['site-eyebrow', 'site-eyebrow-light' => $dark])>{{ $eyebrow }}</p>
        @endif
        <h2 @if ($id) id="{{ $id }}" @endif @class([
            'mt-3 font-display text-3xl leading-tight font-semibold text-balance sm:text-4xl lg:text-[2.75rem]',
            'text-white' => $dark,
            'text-brand-700' => ! $dark,
        ])>{{ $title }}</h2>
        @if ($center)
            <div class="site-rule mx-auto mt-5" aria-hidden="true"></div>
        @endif
        @if ($intro)
            <p @class(['mt-4 text-base leading-relaxed text-pretty sm:text-lg', 'text-brand-100' => $dark, 'text-slate-600' => ! $dark])>{{ $intro }}</p>
        @endif
    </div>
    @if ($link)
        <a href="{{ $link }}" @class(['group inline-flex shrink-0 items-center gap-2 text-sm font-semibold underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4', 'text-gold-300 focus-visible:outline-gold-400' => $dark, 'text-brand-500 focus-visible:outline-brand-500' => ! $dark])>
            {{ $linkLabel }}
            <i class="ph-bold ph-arrow-right transition group-hover:translate-x-0.5" aria-hidden="true"></i>
        </a>
    @endif
</div>
