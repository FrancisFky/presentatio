@extends('layouts.site')
@use('App\Models\Setting')
@use('App\Support\Site')

@php
    $name = Setting::get('ambassador.name');
    $position = Setting::localized('ambassador.position', default: __('site.ambassador.default_position'));
    $photo = Site::media(Setting::get('ambassador.photo'), Site::DEFAULT_AMBASSADOR);
    $message = Setting::localized('ambassador.message');
    $biography = Setting::localized('ambassador.biography');
@endphp

@section('title', __('site.ambassador.title'))
@section('description', $message ?: strip_tags((string) $biography))
@section('image', $photo)

@section('content')
    <section class="site-guilloche relative overflow-hidden bg-brand-700">
        <div class="site-container grid items-end gap-10 pt-12 lg:grid-cols-12 lg:gap-16 lg:pt-16">
            <div class="pb-12 lg:col-span-7 lg:pb-20">
                <nav aria-label="{{ __('site.a11y.breadcrumb') }}">
                    <ol class="flex flex-wrap items-center gap-2 text-[13px] text-brand-200">
                        <li><a href="{{ route('site.home') }}" class="hover:text-white">{{ __('site.nav.home') }}</a></li>
                        <li aria-hidden="true"><i class="ph ph-caret-right text-[10px] text-gold-400"></i></li>
                        <li aria-current="page" class="text-white">{{ __('site.ambassador.title') }}</li>
                    </ol>
                </nav>
                <p class="site-eyebrow site-eyebrow-light mt-10">{{ __('site.ambassador.eyebrow') }}</p>
                <h1 class="mt-3 font-display text-4xl leading-tight font-semibold text-white sm:text-5xl lg:text-6xl">{{ $name ?: __('site.ambassador.title') }}</h1>
                <p class="mt-3 text-lg text-gold-200">{{ $position }}</p>
                @if ($message)
                    <figure class="mt-10 border-s-2 border-gold-400 ps-6">
                        <blockquote class="font-display text-2xl leading-relaxed text-brand-50 italic sm:text-[1.7rem]"><p class="whitespace-pre-line">{{ $message }}</p></blockquote>
                        @if ($signature = Setting::get('ambassador.signature'))
                            <figcaption class="mt-5 font-display text-3xl text-gold-300 italic">{{ $signature }}</figcaption>
                        @endif
                    </figure>
                @endif
            </div>
            <div class="relative mx-auto w-full max-w-sm lg:col-span-5 lg:max-w-none">
                <img src="{{ $photo }}" alt="{{ $name ?: __('site.ambassador.title') }}" class="aspect-[4/5] w-full rounded-t-2xl object-cover shadow-2xl" fetchpriority="high">
            </div>
        </div>
        <div class="h-px bg-gradient-to-r from-transparent via-gold-400/70 to-transparent" aria-hidden="true"></div>
    </section>

    <section class="py-14 sm:py-20" aria-labelledby="bio-title">
        <div class="site-container max-w-3xl">
            <h2 id="bio-title" class="font-display text-3xl font-semibold text-brand-700 sm:text-4xl">{{ __('site.ambassador.biography') }}</h2>
            <div class="site-rule mt-5 ms-0" aria-hidden="true"></div>
            @if ($biography)
                <x-site.prose class="mt-8">{!! $biography !!}</x-site.prose>
            @else
                <p class="mt-8 text-slate-600">{{ __('site.ambassador.no_biography') }}</p>
            @endif
        </div>
    </section>

    @include('site.partials.contact-band')
@endsection
