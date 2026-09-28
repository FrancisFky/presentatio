@extends('layouts.site')

@php
    $title = $album->t('name');
    // Données de la visionneuse : l'URL et la légende de chaque photo
    $slides = $photos->map(fn ($photo) => [
        'src' => $photo->url(),
        'title' => $photo->t('title'),
        'caption' => $photo->t('caption'),
    ])->values();
@endphp

@section('title', $title)
@section('description', $album->t('description') ?: __('site.gallery.intro'))
@section('image', $photos->first()?->url() ?? '')

@section('content')
    <x-site.page-hero :title="$title" :eyebrow="trans_choice('site.gallery.photos_count', $photos->count(), ['count' => $photos->count()])"
        :intro="$album->t('description')" :crumbs="[__('site.gallery.title') => route('site.gallery.index')]" />

    <section class="py-14 sm:py-20"
             x-data="{
                 slides: @js($slides),
                 current: null,
                 open(index) { this.current = index; this.$nextTick(() => this.$refs.close.focus()) },
                 close() { const last = this.current; this.current = null; this.$nextTick(() => document.getElementById('photo-' + last)?.focus()) },
                 next() { this.current = (this.current + 1) % this.slides.length },
                 prev() { this.current = (this.current - 1 + this.slides.length) % this.slides.length },
             }"
             @keydown.escape.window="current !== null && close()"
             @keydown.arrow-right.window="current !== null && next()"
             @keydown.arrow-left.window="current !== null && prev()">
        <div class="site-container">
            @if ($photos->isEmpty())
                <x-site.empty-state icon="ph-image" :title="__('site.gallery.album_empty')" />
            @else
                <h2 class="sr-only">{{ __('site.gallery.photos') }}</h2>
                {{-- Grille régulière : toutes les vignettes au même format, la photo entière s'ouvre dans la visionneuse --}}
                <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($photos as $photo)
                        <li>
                            <figure class="group flex h-full flex-col overflow-hidden rounded-xl bg-brand-50">
                                <a href="{{ $photo->url() }}" id="photo-{{ $loop->index }}" @click.prevent="open({{ $loop->index }})"
                                   class="block overflow-hidden focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500"
                                   aria-label="{{ __('site.gallery.enlarge', ['title' => $photo->t('title') ?: __('site.gallery.photo_n', ['n' => $loop->iteration])]) }}">
                                    <img src="{{ $photo->url() }}" alt="{{ $photo->t('title') ?: $title }}" loading="lazy" class="aspect-[4/3] w-full object-cover transition duration-700 motion-safe:group-hover:scale-[1.03]">
                                </a>
                                @if ($photo->t('title') || $photo->t('caption'))
                                    <figcaption class="flex-1 px-4 py-3 text-sm">
                                        @if ($photo->t('title'))<span class="block font-medium text-brand-700">{{ $photo->t('title') }}</span>@endif
                                        @if ($photo->t('caption'))<span class="block text-slate-600">{{ $photo->t('caption') }}</span>@endif
                                    </figcaption>
                                @endif
                            </figure>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="mt-12">
                <a href="{{ route('site.gallery.index') }}" class="site-link text-sm"><i class="ph-bold ph-arrow-left" aria-hidden="true"></i>{{ __('site.gallery.back') }}</a>
            </div>
        </div>

        {{-- Visionneuse plein écran : Échap pour fermer, flèches pour naviguer --}}
        <template x-if="current !== null">
            <div class="fixed inset-0 z-[60] flex flex-col bg-brand-950/95 backdrop-blur-sm" role="dialog" aria-modal="true" aria-label="{{ __('site.gallery.viewer') }}" x-trap.noscroll="current !== null">
                <div class="flex items-center justify-between px-4 py-3 text-sm text-brand-100 sm:px-6">
                    <span x-text="(current + 1) + ' / ' + slides.length"></span>
                    <button type="button" x-ref="close" @click="close()" class="inline-flex size-11 items-center justify-center rounded-full text-2xl text-white hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-gold-400">
                        <i class="ph ph-x" aria-hidden="true"></i><span class="sr-only">{{ __('site.gallery.close') }}</span>
                    </button>
                </div>
                <div class="relative flex min-h-0 flex-1 items-center justify-center px-4 sm:px-20" @click.self="close()">
                    <img :src="slides[current].src" :alt="slides[current].title || @js($title)" class="max-h-full max-w-full rounded-lg object-contain shadow-2xl">
                    <template x-if="slides.length > 1">
                        <div>
                            <button type="button" @click="prev()" class="absolute top-1/2 left-2 inline-flex size-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-2xl text-white hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-gold-400 sm:left-6">
                                <i class="ph ph-caret-left" aria-hidden="true"></i><span class="sr-only">{{ __('site.gallery.previous') }}</span>
                            </button>
                            <button type="button" @click="next()" class="absolute top-1/2 right-2 inline-flex size-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-2xl text-white hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-gold-400 sm:right-6">
                                <i class="ph ph-caret-right" aria-hidden="true"></i><span class="sr-only">{{ __('site.gallery.next') }}</span>
                            </button>
                        </div>
                    </template>
                </div>
                <div class="min-h-16 px-6 py-4 text-center" aria-live="polite">
                    <p class="font-display text-xl text-white" x-text="slides[current].title"></p>
                    <p class="mt-1 text-sm text-brand-200" x-text="slides[current].caption"></p>
                </div>
            </div>
        </template>
    </section>
@endsection
