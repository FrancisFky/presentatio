@extends('layouts.site')
@use('App\Support\Site')

@php
    $title = $news->t('title');
    $image = Site::media($news->image_path);
@endphp

@section('title', $title)
@section('description', $news->t('excerpt') ?: strip_tags((string) $news->t('body')))
@section('image', $image ?? '')
@section('og_type', 'article')

@push('head')
    <meta property="article:published_time" content="{{ $news->published_on->toDateString() }}">
@endpush

@section('content')
    <article>
        <header class="bg-paper">
            <div class="site-container max-w-4xl pt-10 pb-12 sm:pt-14">
                <nav aria-label="{{ __('site.a11y.breadcrumb') }}">
                    <ol class="flex flex-wrap items-center gap-2 text-[13px] text-slate-500">
                        <li><a href="{{ route('site.home') }}" class="hover:text-brand-600">{{ __('site.nav.home') }}</a></li>
                        <li aria-hidden="true"><i class="ph ph-caret-right text-[10px] text-gold-600"></i></li>
                        <li><a href="{{ route('site.news.index') }}" class="hover:text-brand-600">{{ __('site.news.title') }}</a></li>
                    </ol>
                </nav>
                <div class="mt-8 flex flex-wrap items-center gap-3 text-sm">
                    @if ($news->category)
                        <a href="{{ route('site.news.index', ['category' => $news->category->id]) }}" class="rounded-full bg-brand-700 px-3 py-1 text-xs font-semibold tracking-wider text-white uppercase hover:bg-brand-500">{{ $news->category->t('name') }}</a>
                    @endif
                    <time datetime="{{ $news->published_on->toDateString() }}" class="text-slate-600">{{ $news->published_on->translatedFormat('l j F Y') }}</time>
                    @if ($news->author)
                        <span class="text-slate-400" aria-hidden="true">·</span>
                        <span class="text-slate-600">{{ __('site.news.by', ['author' => $news->author]) }}</span>
                    @endif
                </div>
                <h1 class="mt-5 font-display text-4xl leading-[1.1] font-semibold text-balance text-brand-700 sm:text-5xl lg:text-[3.4rem]">{{ $title }}</h1>
                @if ($excerpt = $news->t('excerpt'))
                    <p class="mt-6 text-lg leading-relaxed text-slate-700 sm:text-xl">{{ $excerpt }}</p>
                @endif
            </div>
        </header>

        <div class="site-container max-w-4xl pb-16">
            @if ($image)
                <figure class="-mt-2 overflow-hidden rounded-2xl shadow-xl">
                    <img src="{{ $image }}" alt="" class="aspect-[16/9] w-full object-cover">
                </figure>
            @endif

            @if ($body = $news->t('body'))
                <x-site.prose class="mt-12">{!! $body !!}</x-site.prose>
            @endif

            @if ($news->attachment_path)
                <a href="{{ Site::media($news->attachment_path) }}" target="_blank" rel="noopener" class="mt-10 flex items-center gap-4 rounded-xl bg-paper p-5 ring-1 ring-gold-200 transition hover:ring-gold-400">
                    <span class="flex size-12 items-center justify-center rounded-lg bg-red-50 text-2xl text-red-700" aria-hidden="true"><i class="ph ph-file-pdf"></i></span>
                    <span class="flex-1">
                        <span class="block font-semibold text-brand-700">{{ __('site.news.attachment') }}</span>
                        <span class="text-sm text-slate-500">PDF · {{ __('site.a11y.new_window') }}</span>
                    </span>
                    <i class="ph-bold ph-download-simple text-xl text-brand-500" aria-hidden="true"></i>
                </a>
            @endif

            <footer class="mt-12 flex flex-col gap-6 border-t border-brand-900/10 pt-8 sm:flex-row sm:items-center sm:justify-between">
                <x-site.share :url="route('site.news.show', $news)" :title="$title" />
                <a href="{{ route('site.news.index') }}" class="site-link text-sm"><i class="ph-bold ph-arrow-left" aria-hidden="true"></i>{{ __('site.news.back') }}</a>
            </footer>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="bg-paper py-16 sm:py-20" aria-labelledby="related-title">
            <div class="site-container">
                <x-site.section-heading id="related-title" :title="__('site.news.related')" :link="route('site.news.index')" :link-label="__('site.home.all_news')" />
                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $item)
                        <x-site.news-card :news="$item" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
