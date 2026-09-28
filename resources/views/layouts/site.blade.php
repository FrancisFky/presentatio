{{--
    Gabarit du site public. Chaque page renseigne :
      @section('title')        titre de la page (sans le nom de l'ambassade)
      @section('description')  résumé pour Google et les partages
      @section('image')        URL absolue de l'image de partage
      @section('og_type')      « article » pour une actualité, « website » sinon
--}}
@use('App\Models\Setting')
@use('App\Support\Locales')
@use('App\Support\Site')
@use('Illuminate\Support\Str')
@php
    $locale = app()->getLocale();
    $siteName = Site::name();
    // Les @section('x', $valeur) arrivent déjà échappées : on les décode avant de les ré-échapper
    $yield = fn (string $section) => trim(html_entity_decode($__env->yieldContent($section), ENT_QUOTES | ENT_HTML5));
    $pageTitle = $yield('title');
    $fullTitle = $pageTitle !== '' ? $pageTitle . ' — ' . $siteName : $siteName;
    $description = Str::limit(
        trim(preg_replace('/\s+/', ' ', strip_tags($yield('description')))) ?: Setting::localized('site.seo_description', default: __('site.meta.description')),
        180,
    );
    $shareImage = $yield('image') ?: asset(Site::DEFAULT_HERO);
    $canonical = Site::localizedUrl($locale, Site::CANONICAL_QUERY);
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $canonical }}">
    @foreach (Locales::SUPPORTED as $alternate)
        <link rel="alternate" hreflang="{{ $alternate }}" href="{{ Site::localizedUrl($alternate, Site::CANONICAL_QUERY) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ Site::localizedUrl(Locales::DEFAULT, Site::CANONICAL_QUERY) }}">

    {{-- Partages sur les réseaux sociaux et messageries --}}
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:type" content="{{ $yield('og_type') ?: 'website' }}">
    <meta property="og:title" content="{{ $pageTitle ?: $siteName }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta property="og:locale" content="{{ $locale === 'fr' ? 'fr_FR' : 'en_GB' }}">
    <meta property="og:locale:alternate" content="{{ $locale === 'fr' ? 'en_GB' : 'fr_FR' }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle ?: $siteName }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $shareImage }}">

    <meta name="theme-color" content="#112233">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('images/armoiries.svg') }}" type="image/svg+xml">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('head')
</head>
<body class="min-h-screen bg-white font-sans text-ink antialiased selection:bg-gold-200 selection:text-brand-900">
    <a href="#contenu"
       class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:rounded-md focus:bg-gold-400 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-brand-900 focus:shadow-lg">
        {{ __('site.a11y.skip') }}
    </a>

    <div class="flag-stripe h-1 print:hidden" aria-hidden="true"></div>

    @include('site.partials.header')

    <main id="contenu" tabindex="-1" class="focus:outline-none">
        @yield('content')
    </main>

    @include('site.partials.footer')

    @livewireScripts
    @stack('scripts')
</body>
</html>
