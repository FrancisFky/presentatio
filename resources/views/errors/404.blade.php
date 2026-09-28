@php
    // Une adresse inconnue n'est passée par aucun middleware : on déduit la
    // langue de l'URL (/en/…), sinon celle déjà active, sinon le français
    $segment = request()->segment(1);
    $errorLocale = \App\Support\Locales::isSupported($segment) ? $segment
        : (\App\Support\Locales::isSupported(app()->getLocale()) ? app()->getLocale() : \App\Support\Locales::DEFAULT);
    app()->setLocale($errorLocale);
    \Illuminate\Support\Facades\URL::defaults(['locale' => $errorLocale]);
@endphp
@extends('layouts.site')

@section('title', __('site.errors.404_title'))

@push('head')
    <meta name="robots" content="noindex">
@endpush

@section('content')
    <section class="site-guilloche relative overflow-hidden bg-paper">
        <div class="site-container flex min-h-[60vh] flex-col items-center justify-center py-20 text-center sm:py-28">
            <img src="{{ asset('images/armoiries.svg') }}" alt="" class="size-20 opacity-90" width="80" height="80">
            <p class="mt-8 font-display text-8xl leading-none font-semibold text-gold-400 sm:text-9xl" aria-hidden="true">404</p>
            <h1 class="mt-4 font-display text-3xl font-semibold text-brand-700 sm:text-4xl">{{ __('site.errors.404_title') }}</h1>
            <p class="mt-4 max-w-xl text-slate-600">{{ __('site.errors.404_text') }}</p>
            <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('site.home') }}" class="site-btn site-btn-navy"><i class="ph-bold ph-house" aria-hidden="true"></i>{{ __('site.errors.back_home') }}</a>
                <a href="{{ route('site.contact') }}" class="site-btn site-btn-outline">{{ __('site.nav.contact') }}</a>
            </div>
            <ul class="mt-12 flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm">
                <li><a href="{{ route('site.services.index') }}" class="site-link">{{ __('site.nav.services') }}</a></li>
                <li><a href="{{ route('site.appointment') }}" class="site-link">{{ __('site.cta.appointment') }}</a></li>
                <li><a href="{{ route('site.news.index') }}" class="site-link">{{ __('site.nav.news') }}</a></li>
                <li><a href="{{ route('site.documents.index') }}" class="site-link">{{ __('site.nav.documents') }}</a></li>
            </ul>
        </div>
    </section>
@endsection
