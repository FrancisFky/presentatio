@extends('layouts.site')
@use('App\Models\Setting')
@use('App\Support\Site')
@use('Illuminate\Support\Str')

@php
    $heroTitle = Setting::localized('home.hero_title', default: __('site.home.hero_title'));
    $heroSubtitle = Setting::localized('home.hero_subtitle', default: __('site.home.hero_subtitle'));
    $heroImage = Site::media(Setting::get('home.hero_image'), Site::DEFAULT_HERO);

    $welcome = Setting::localized('home.welcome');
    $pillars = array_filter([
        'mission' => Setting::localized('home.mission'),
        'vision' => Setting::localized('home.vision'),
        'objectives' => Setting::localized('home.objectives'),
    ]);

    $ambassadorMessage = Site::ambassadorPublished() ? Setting::localized('ambassador.message') : null;
    $emergency = Site::emergency();
    $phones = Site::phones();
@endphp

@section('description', Setting::localized('site.seo_description', default: $heroSubtitle))
@section('image', $heroImage)

@section('content')
    {{-- 1. Bandeau d'accueil --}}
    <section class="relative isolate overflow-hidden bg-brand-900" aria-labelledby="hero-title">
        <img src="{{ $heroImage }}" alt="" class="site-ken-burns absolute inset-0 -z-20 size-full object-cover" fetchpriority="high">
        <div class="absolute inset-0 -z-10 bg-gradient-to-r from-brand-950/95 via-brand-900/80 to-brand-900/30" aria-hidden="true"></div>
        <div class="absolute inset-x-0 bottom-0 -z-10 h-2/3 bg-gradient-to-t from-brand-950/80 to-transparent" aria-hidden="true"></div>
        {{-- Sur téléphone le texte couvre toute la photo : voile uniforme pour la lisibilité --}}
        <div class="absolute inset-0 -z-10 bg-brand-950/45 sm:hidden" aria-hidden="true"></div>

        <div class="site-container flex flex-col justify-center py-20 sm:min-h-[640px] lg:min-h-[720px] lg:py-28">
            <div class="max-w-3xl">
                <h1 id="hero-title" class="site-rise site-rise-delay mt-5 font-display text-[2.6rem] leading-[1.04] font-semibold text-balance text-white sm:text-6xl lg:text-7xl">
                    {{ $heroTitle }}
                </h1>
                <p class="site-rise site-rise-delay mt-6 max-w-2xl text-lg leading-relaxed text-pretty text-brand-100 sm:text-xl">{{ $heroSubtitle }}</p>
                <div class="site-rise site-rise-delay-2 mt-10 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('site.appointment') }}" class="site-btn site-btn-gold px-7 py-3.5 text-[15px]">
                        <i class="ph-bold ph-calendar-check" aria-hidden="true"></i>{{ __('site.cta.appointment') }}
                    </a>
                    <a href="{{ route('site.services.index') }}" class="site-btn site-btn-light px-7 py-3.5 text-[15px]">
                        {{ __('site.cta.services') }}<i class="ph-bold ph-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            {{-- Infos pratiques, directement sous les boutons --}}
            <dl class="site-rise site-rise-delay-2 mt-16 grid max-w-4xl gap-px overflow-hidden rounded-xl bg-white/10 ring-1 ring-white/15 backdrop-blur-md sm:grid-cols-3">
                @foreach ([
                    ['ph-clock', __('site.contact.hours'), Site::hours()],
                    ['ph-phone', __('site.contact.phone'), $phones[0]],
                    ['ph-map-pin', __('site.contact.address'), Str::before(Site::address(), "\n")],
                ] as [$icon, $label, $value])
                    <div class="flex items-start gap-3 bg-brand-950/40 px-5 py-4">
                        <i class="ph {{ $icon }} mt-0.5 text-xl text-gold-300" aria-hidden="true"></i>
                        <div class="min-w-0">
                            <dt class="text-[11px] font-semibold tracking-[0.18em] text-gold-200/90 uppercase">{{ $label }}</dt>
                            <dd class="mt-0.5 text-sm text-white">{{ $value }}</dd>
                        </div>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- 2. Communiqués importants --}}
    @if ($announcements->isNotEmpty())
        <section class="border-b border-brand-900/5 bg-paper" aria-labelledby="notices-title">
            <div class="site-container py-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:gap-8">
                    <h2 id="notices-title" class="flex shrink-0 items-center gap-2 text-xs font-semibold tracking-[0.2em] text-brand-600 uppercase">
                        <i class="ph-fill ph-megaphone text-lg text-gold-600" aria-hidden="true"></i>{{ __('site.home.notices') }}
                    </h2>
                    <ul class="flex flex-1 flex-col divide-y divide-brand-900/5 lg:flex-row lg:divide-x lg:divide-y-0">
                        @foreach ($announcements as $announcement)
                            <li @class(['relative flex-1 py-3 lg:px-6 lg:py-1', 'lg:ps-0' => $loop->first])>
                                <a href="{{ route('site.announcements.show', $announcement) }}" class="group flex items-start gap-3 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-500">
                                    @if ($announcement->priority === 'urgent')
                                        <span class="mt-1.5 size-2 shrink-0 rounded-full bg-flag-red ring-4 ring-flag-red/15" aria-hidden="true"></span>
                                    @else
                                        <span class="mt-1.5 size-2 shrink-0 rounded-full bg-gold-400 ring-4 ring-gold-400/20" aria-hidden="true"></span>
                                    @endif
                                    <span class="min-w-0">
                                        @if ($announcement->priority === 'urgent')
                                            <span class="me-1 text-xs font-bold tracking-wider text-red-700 uppercase">{{ __('site.announcements.priority.urgent') }} ·</span>
                                        @endif
                                        <span class="font-medium text-brand-700 group-hover:underline decoration-gold-400 underline-offset-4">{{ $announcement->t('title') }}</span>
                                        <time datetime="{{ $announcement->published_on->toDateString() }}" class="mt-0.5 block text-xs text-slate-500">{{ $announcement->published_on->translatedFormat('j F Y') }}</time>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('site.announcements.index') }}" class="site-link shrink-0 text-sm">{{ __('site.home.all_notices') }}<i class="ph-bold ph-arrow-right" aria-hidden="true"></i></a>
                </div>
            </div>
        </section>
    @endif

    {{-- 3. Services consulaires --}}
    @if ($services->isNotEmpty())
        <section class="py-20 sm:py-24" aria-labelledby="services-title">
            <div class="site-container">
                <x-site.section-heading id="services-title" :eyebrow="__('site.home.services_eyebrow')" :title="__('site.services.title')" :intro="__('site.services.intro')"
                    :link="route('site.services.index')" :link-label="__('site.home.all_services')" />
                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($services as $service)
                        <x-site.service-card :service="$service" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 4. Mot de l'ambassadeur --}}
    @if ($ambassadorMessage)
        <section class="site-guilloche relative overflow-hidden bg-brand-700 py-20 sm:py-28" aria-labelledby="ambassador-title">
            <div class="site-container grid items-center gap-12 lg:grid-cols-12 lg:gap-16">
                <div class="relative mx-auto w-full max-w-sm lg:col-span-5 lg:max-w-none">
                    <div class="absolute -inset-3 translate-x-4 translate-y-4 rounded-2xl border border-gold-400/50" aria-hidden="true"></div>
                    <img src="{{ Site::media(Setting::get('ambassador.photo'), Site::DEFAULT_AMBASSADOR) }}" alt="{{ Setting::get('ambassador.name', __('site.ambassador.title')) }}" loading="lazy"
                         class="relative aspect-[4/5] w-full rounded-2xl object-cover shadow-2xl">
                </div>
                <div class="lg:col-span-7">
                    <h2 id="ambassador-title" class="font-display text-3xl font-semibold text-white sm:text-4xl lg:text-5xl">{{ __('site.ambassador.title') }}</h2>
                    <figure class="mt-8">
                        <i class="ph-fill ph-quotes text-5xl text-gold-400/70" aria-hidden="true"></i>
                        <blockquote class="mt-2 font-display text-2xl leading-relaxed text-brand-50 italic sm:text-[1.7rem]">
                            <p class="whitespace-pre-line">{{ $ambassadorMessage }}</p>
                        </blockquote>
                        <figcaption class="mt-8 flex flex-wrap items-end justify-between gap-6 border-t border-white/10 pt-6">
                            <div>
                                @if ($signature = Setting::get('ambassador.signature'))
                                    <p class="font-display text-3xl text-gold-300 italic">{{ $signature }}</p>
                                @endif
                                @if ($name = Setting::get('ambassador.name'))
                                    <p class="mt-1 font-semibold text-white">{{ $name }}</p>
                                @endif
                                <p class="text-sm text-brand-200">{{ Setting::localized('ambassador.position', default: __('site.ambassador.default_position')) }}</p>
                            </div>
                            <a href="{{ route('site.ambassador') }}" class="site-btn site-btn-light">{{ __('site.ambassador.read_bio') }}<i class="ph-bold ph-arrow-right" aria-hidden="true"></i></a>
                        </figcaption>
                    </figure>
                </div>
            </div>
        </section>
    @endif

    {{-- 5. L'ambassade : bienvenue, mission, vision, objectifs --}}
    @if ($welcome || $pillars)
        <section class="bg-paper py-20 sm:py-24" aria-labelledby="embassy-title">
            <div class="site-container">
                @if ($welcome)
                    <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
                        <div>
                            <p class="site-eyebrow">{{ __('site.home.welcome_eyebrow') }}</p>
                            <h2 id="embassy-title" class="mt-3 font-display text-3xl font-semibold text-brand-700 sm:text-4xl lg:text-[2.75rem]">{{ __('site.home.welcome_title') }}</h2>
                            <p class="mt-6 text-lg leading-relaxed whitespace-pre-line text-slate-700">{{ $welcome }}</p>
                            <a href="{{ route('site.page', 'about-embassy') }}" class="site-link mt-8">{{ __('site.home.discover_embassy') }}<i class="ph-bold ph-arrow-right" aria-hidden="true"></i></a>
                        </div>
                        <div class="relative">
                            <img src="{{ asset('images/site/ambassade-4.jpg') }}" alt="{{ __('site.home.embassy_photo_alt') }}" loading="lazy" class="aspect-[4/3] w-full rounded-2xl object-cover shadow-xl">
                            <div class="absolute -bottom-6 -left-4 hidden rounded-xl bg-brand-700 px-6 py-5 text-white shadow-xl sm:block">
                                <p class="font-display text-3xl font-semibold text-gold-300">Nairobi</p>
                                <p class="text-xs tracking-[0.18em] text-brand-100 uppercase">{{ __('site.home.location_label') }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <x-site.section-heading id="embassy-title" center :eyebrow="__('site.home.welcome_eyebrow')" :title="__('site.home.welcome_title')" />
                @endif

                @if ($pillars)
                    <div @class(['grid gap-6 md:grid-cols-3', 'mt-20' => $welcome, 'mt-12' => ! $welcome])>
                        @foreach ($pillars as $key => $text)
                            <article class="relative site-card p-7 sm:p-8">
                                <span class="font-display text-5xl font-semibold text-gold-300" aria-hidden="true">0{{ $loop->iteration }}</span>
                                <h3 class="mt-3 font-display text-2xl font-semibold text-brand-700">{{ __("site.home.{$key}") }}</h3>
                                <div class="mt-3 h-px w-10 bg-gold-400" aria-hidden="true"></div>
                                <p class="mt-4 text-[15px] leading-relaxed whitespace-pre-line text-slate-600">{{ $text }}</p>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- 6. Actualités --}}
    @if ($news->isNotEmpty())
        <section class="py-20 sm:py-24" aria-labelledby="news-title">
            <div class="site-container">
                <x-site.section-heading id="news-title" :eyebrow="__('site.home.news_eyebrow')" :title="__('site.news.title')"
                    :link="route('site.news.index')" :link-label="__('site.home.all_news')" />
                <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($news as $item)
                        <x-site.news-card :news="$item" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 7. Événements à venir --}}
    @if ($events->isNotEmpty())
        <section class="bg-paper py-20 sm:py-24" aria-labelledby="events-title">
            <div class="site-container">
                <x-site.section-heading id="events-title" :eyebrow="__('site.home.events_eyebrow')" :title="__('site.events.upcoming')"
                    :link="route('site.events.index')" :link-label="__('site.home.all_events')" />
                <div class="mt-12 grid gap-5 lg:grid-cols-3">
                    @foreach ($events as $event)
                        <x-site.event-card :event="$event" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 8. Galerie : mosaïque, les photos mises en avant d'abord --}}
    @if ($photos->count() >= 3)
        <section class="py-20 sm:py-24" aria-labelledby="gallery-title">
            <div class="site-container">
                <x-site.section-heading id="gallery-title" :eyebrow="__('site.home.gallery_eyebrow')" :title="__('site.gallery.title')"
                    :link="route('site.gallery.index')" :link-label="__('site.home.all_gallery')" />
                <ul class="mt-12 grid auto-rows-[140px] grid-cols-2 gap-3 sm:auto-rows-[180px] md:grid-cols-4 lg:auto-rows-[200px]">
                    @foreach ($photos as $photo)
                        <li @class(['group relative overflow-hidden rounded-xl bg-brand-100', 'col-span-2 row-span-2' => $loop->first])>
                            <a href="{{ $photo->album ? route('site.gallery.show', $photo->album) : route('site.gallery.index') }}" class="block size-full focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500">
                                <img src="{{ $photo->url() }}" alt="{{ $photo->t('title') ?: ($photo->album?->t('name') ?? __('site.gallery.photo')) }}" loading="lazy"
                                     class="size-full object-cover transition duration-700 motion-safe:group-hover:scale-105">
                                @if ($caption = $photo->t('title') ?: $photo->album?->t('name'))
                                    <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-brand-950/85 to-transparent p-4 pt-10 text-sm font-medium text-white opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100" aria-hidden="true">{{ $caption }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- 9 et 10. Documents utiles et jours de fermeture --}}
    @if ($documents->isNotEmpty() || $holidays->isNotEmpty())
        <section class="bg-paper py-20 sm:py-24">
            <div class="site-container grid gap-12 lg:grid-cols-12 lg:gap-10">
                @if ($documents->isNotEmpty())
                    <div @class(['lg:col-span-7' => $holidays->isNotEmpty(), 'lg:col-span-12' => $holidays->isEmpty()]) aria-labelledby="documents-title" role="region">
                        <x-site.section-heading id="documents-title" :eyebrow="__('site.home.documents_eyebrow')" :title="__('site.documents.title')"
                            :link="route('site.documents.index')" :link-label="__('site.home.all_documents')" />
                        <ul class="mt-10 space-y-3">
                            @foreach ($documents as $document)
                                <x-site.document-item :document="$document" />
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($holidays->isNotEmpty())
                    <div @class(['lg:col-span-5' => $documents->isNotEmpty(), 'lg:col-span-12' => $documents->isEmpty()]) aria-labelledby="holidays-title" role="region">
                        <div class="h-full rounded-2xl bg-brand-700 p-7 text-white shadow-xl sm:p-9">
                            <p class="site-eyebrow site-eyebrow-light">{{ __('site.holidays.eyebrow') }}</p>
                            <h2 id="holidays-title" class="mt-3 font-display text-3xl font-semibold">{{ __('site.holidays.title') }}</h2>
                            <p class="mt-2 text-sm text-brand-100">{{ __('site.holidays.intro') }}</p>
                            <ul class="mt-8 space-y-4">
                                @foreach ($holidays as $holiday)
                                    <li class="flex items-center gap-4">
                                        <x-site.date-badge :date="$holiday->date" dark class="w-16 py-2" />
                                        <div>
                                            <p class="font-medium text-white">{{ $holiday->t('name') }}</p>
                                            <p class="text-sm text-brand-200 first-letter:uppercase">{{ $holiday->date->translatedFormat('l j F Y') }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- 11. Nous joindre --}}
    @include('site.partials.contact-band')
@endsection
