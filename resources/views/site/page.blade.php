@extends('layouts.site')
@use('App\Support\Site')

@php
    $title = $page->t('title');
    $image = Site::media($page->image_path, Site::PAGE_IMAGES[$page->slug] ?? Site::DEFAULT_HERO);
    $body = $page->t('body');
@endphp

@section('title', $title)
@section('description', strip_tags((string) $body))
@section('image', $image)

@section('content')
    <x-site.page-hero :title="$title" :eyebrow="__('site.nav.about')" :image="$image" />

    <article class="py-14 sm:py-20">
        <div class="site-container max-w-3xl">
            @if ($body)
                <x-site.prose>{!! $body !!}</x-site.prose>
            @else
                <x-site.empty-state icon="ph-note-pencil" :title="__('site.pages.empty_title')" :text="__('site.pages.empty_text')" />
            @endif
        </div>
    </article>

    @if ($siblings->isNotEmpty() || Site::ambassadorPublished())
        <section class="bg-paper py-16 sm:py-20" aria-labelledby="more-title">
            <div class="site-container">
                <x-site.section-heading id="more-title" :eyebrow="__('site.nav.about')" :title="__('site.pages.more')" />
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach ($siblings as $sibling)
                        <a href="{{ route('site.page', $sibling) }}" class="group relative block overflow-hidden rounded-2xl bg-brand-900 shadow-lg focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-500">
                            <img src="{{ Site::media($sibling->image_path, Site::PAGE_IMAGES[$sibling->slug] ?? Site::DEFAULT_HERO) }}" alt="" loading="lazy" class="aspect-[4/3] w-full object-cover opacity-80 transition duration-700 motion-safe:group-hover:scale-105">
                            <span class="absolute inset-0 bg-gradient-to-t from-brand-950/90 to-transparent" aria-hidden="true"></span>
                            <span class="absolute inset-x-0 bottom-0 flex items-center justify-between p-6 font-display text-2xl font-semibold text-white">
                                {{ $sibling->t('title') }}<i class="ph-bold ph-arrow-right text-lg text-gold-300 transition group-hover:translate-x-1" aria-hidden="true"></i>
                            </span>
                        </a>
                    @endforeach
                    @if (Site::ambassadorPublished())
                        <a href="{{ route('site.ambassador') }}" class="group relative block overflow-hidden rounded-2xl bg-brand-900 shadow-lg focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-500">
                            <img src="{{ Site::media(\App\Models\Setting::get('ambassador.photo'), Site::DEFAULT_AMBASSADOR) }}" alt="" loading="lazy" class="aspect-[4/3] w-full object-cover opacity-80 transition duration-700 motion-safe:group-hover:scale-105">
                            <span class="absolute inset-0 bg-gradient-to-t from-brand-950/90 to-transparent" aria-hidden="true"></span>
                            <span class="absolute inset-x-0 bottom-0 flex items-center justify-between p-6 font-display text-2xl font-semibold text-white">
                                {{ __('site.nav.ambassador') }}<i class="ph-bold ph-arrow-right text-lg text-gold-300 transition group-hover:translate-x-1" aria-hidden="true"></i>
                            </span>
                        </a>
                    @endif
                </div>
            </div>
        </section>
    @endif
@endsection
