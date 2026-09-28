@extends('layouts.site')

@section('title', __('site.gallery.title'))
@section('description', __('site.gallery.intro'))

@section('content')
    <x-site.page-hero :title="__('site.gallery.title')" :eyebrow="__('site.gallery.eyebrow')" :intro="__('site.gallery.intro')" :image="asset('images/site/presidents-congo-kenya.jpg')" />

    <section class="py-14 sm:py-20">
        <div class="site-container">
            @if ($albums->isEmpty())
                <x-site.empty-state icon="ph-images" :title="__('site.gallery.empty_title')" :text="__('site.gallery.empty_text')" />
            @else
                <h2 class="sr-only">{{ __('site.gallery.albums') }}</h2>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($albums as $album)
                        <article class="group relative overflow-hidden rounded-2xl bg-brand-900 shadow-lg">
                            <div class="aspect-[4/3]">
                                @if ($album->cover)
                                    <img src="{{ $album->cover->url() }}" alt="" loading="lazy" class="size-full object-cover opacity-90 transition duration-700 group-hover:opacity-100 motion-safe:group-hover:scale-105">
                                @endif
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-brand-950/90 via-brand-950/20 to-transparent" aria-hidden="true"></div>
                            <div class="absolute inset-x-0 bottom-0 p-6">
                                <p class="inline-flex items-center gap-1.5 text-xs font-semibold tracking-[0.18em] text-gold-300 uppercase">
                                    <i class="ph ph-images" aria-hidden="true"></i>{{ trans_choice('site.gallery.photos_count', $album->photos_count, ['count' => $album->photos_count]) }}
                                </p>
                                <h3 class="mt-2 font-display text-2xl leading-snug font-semibold text-white">
                                    <a href="{{ route('site.gallery.show', $album) }}" class="after:absolute after:inset-0 focus-visible:outline-none group-focus-within:underline decoration-gold-400 underline-offset-4">{{ $album->t('name') }}</a>
                                </h3>
                                @if ($description = $album->t('description'))
                                    <p class="mt-1 line-clamp-2 text-sm text-brand-100">{{ $description }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
                {{ $albums->links('site.partials.pagination') }}
            @endif
        </div>
    </section>
@endsection
