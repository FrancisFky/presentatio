@extends('layouts.site')
@use('App\Support\Site')

@php
    $phones = Site::phones();
    $emergency = Site::emergency();
@endphp

@section('title', __('site.contact.title'))
@section('description', __('site.contact.intro'))

@section('content')
    <x-site.page-hero :title="__('site.contact.title')" :eyebrow="__('site.contact.eyebrow')" :intro="__('site.contact.intro')" :image="asset('images/site/bureaux-1.jpg')" />

    {{-- Coordonnées --}}
    <section class="relative z-10 -mt-10 pb-4" aria-labelledby="coords-title">
        <h2 id="coords-title" class="sr-only">{{ __('site.contact.coordinates') }}</h2>
        <div class="site-container grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="site-card p-6">
                <i class="ph ph-map-pin text-2xl text-gold-600" aria-hidden="true"></i>
                <h3 class="mt-3 text-xs font-semibold tracking-[0.18em] text-slate-500 uppercase">{{ __('site.contact.address') }}</h3>
                <p class="mt-1.5 whitespace-pre-line text-brand-700">{{ Site::address() }}</p>
                <a href="{{ Site::mapUrl() }}" target="_blank" rel="noopener noreferrer" class="site-link mt-3 text-sm">{{ __('site.contact.open_map') }}<i class="ph ph-arrow-square-out" aria-hidden="true"></i><span class="sr-only">({{ __('site.a11y.new_window') }})</span></a>
            </div>
            <div class="site-card p-6">
                <i class="ph ph-phone text-2xl text-gold-600" aria-hidden="true"></i>
                <h3 class="mt-3 text-xs font-semibold tracking-[0.18em] text-slate-500 uppercase">{{ __('site.contact.phone') }}</h3>
                <ul class="mt-1.5 space-y-0.5">
                    @foreach ($phones as $phone)
                        <li><a href="{{ Site::tel($phone) }}" class="text-brand-700 hover:text-gold-700">{{ $phone }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="site-card p-6">
                <i class="ph ph-envelope-simple text-2xl text-gold-600" aria-hidden="true"></i>
                <h3 class="mt-3 text-xs font-semibold tracking-[0.18em] text-slate-500 uppercase">{{ __('site.contact.email') }}</h3>
                <a href="mailto:{{ Site::email() }}" class="mt-1.5 block break-all text-brand-700 hover:text-gold-700">{{ Site::email() }}</a>
            </div>
            <div class="site-card p-6">
                <i class="ph ph-clock text-2xl text-gold-600" aria-hidden="true"></i>
                <h3 class="mt-3 text-xs font-semibold tracking-[0.18em] text-slate-500 uppercase">{{ __('site.contact.hours') }}</h3>
                <p class="mt-1.5 whitespace-pre-line text-brand-700">{{ Site::hours() }}</p>
            </div>
        </div>
    </section>

    <div class="site-container grid gap-12 py-14 sm:py-20 lg:grid-cols-12 lg:gap-12">
        {{-- Formulaire --}}
        <section class="lg:col-span-7" aria-labelledby="form-title">
            <p class="site-eyebrow">{{ __('site.contact.form_eyebrow') }}</p>
            <h2 id="form-title" class="mt-3 font-display text-3xl font-semibold text-brand-700 sm:text-4xl">{{ __('site.contact.form_title') }}</h2>
            <p class="mt-3 text-slate-600">{{ __('site.contact.form_intro') }}</p>
            <div class="mt-8">
                <livewire:contact-form />
            </div>
        </section>

        <div class="space-y-6 lg:col-span-5">
            {{-- Urgence consulaire --}}
            <section id="urgence" class="scroll-mt-28 overflow-hidden rounded-2xl bg-brand-900 text-white shadow-xl" aria-labelledby="emergency-title">
                <div class="flex items-center gap-3 bg-flag-red px-6 py-4">
                    <i class="ph-fill ph-siren text-2xl" aria-hidden="true"></i>
                    <h2 id="emergency-title" class="font-display text-2xl font-semibold">{{ __('site.emergency.title') }}</h2>
                </div>
                <div class="p-6 sm:p-7">
                    <p class="text-sm leading-relaxed text-brand-100">{{ $emergency['instructions'] ?? __('site.emergency.default_instructions') }}</p>
                    <dl class="mt-6 space-y-4">
                        <div>
                            <dt class="text-xs font-semibold tracking-[0.18em] text-red-200 uppercase">{{ __('site.emergency.hotline') }}</dt>
                            <dd><a href="{{ Site::tel($emergency['hotline']) }}" class="font-display text-3xl font-semibold text-white hover:text-gold-200">{{ $emergency['hotline'] }}</a></dd>
                        </div>
                        @if (! empty($emergency['whatsapp']))
                            <div>
                                <dt class="text-xs font-semibold tracking-[0.18em] text-red-200 uppercase">WhatsApp</dt>
                                <dd><a href="{{ Site::whatsapp($emergency['whatsapp']) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-lg text-white hover:text-gold-200"><i class="ph ph-whatsapp-logo" aria-hidden="true"></i>{{ $emergency['whatsapp'] }}</a></dd>
                            </div>
                        @endif
                        @if (! empty($emergency['email']))
                            <div>
                                <dt class="text-xs font-semibold tracking-[0.18em] text-red-200 uppercase">{{ __('site.contact.email') }}</dt>
                                <dd><a href="mailto:{{ $emergency['email'] }}" class="break-all text-white hover:text-gold-200">{{ $emergency['email'] }}</a></dd>
                            </div>
                        @endif
                        @if (! empty($emergency['duty_officer']))
                            <div>
                                <dt class="text-xs font-semibold tracking-[0.18em] text-red-200 uppercase">{{ __('site.emergency.duty_officer') }}</dt>
                                <dd class="text-white">{{ $emergency['duty_officer'] }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </section>

            {{-- Plan d'accès --}}
            <a href="{{ Site::mapUrl() }}" target="_blank" rel="noopener noreferrer" class="group relative block overflow-hidden rounded-2xl shadow-lg focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-500">
                <img src="{{ asset('images/site/bureaux-3.jpg') }}" alt="" loading="lazy" class="aspect-[16/10] w-full object-cover transition duration-700 motion-safe:group-hover:scale-105">
                <span class="absolute inset-0 bg-gradient-to-t from-brand-950/90 via-brand-950/30 to-transparent" aria-hidden="true"></span>
                <span class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-4 p-6">
                    <span>
                        <span class="block text-xs font-semibold tracking-[0.18em] text-gold-300 uppercase">{{ __('site.contact.find_us') }}</span>
                        <span class="mt-1 block font-display text-2xl font-semibold text-white">{{ \Illuminate\Support\Str::before(Site::address(), "\n") }}</span>
                    </span>
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-gold-400 text-brand-900" aria-hidden="true"><i class="ph-bold ph-map-trifold text-xl"></i></span>
                </span>
                <span class="sr-only">{{ __('site.contact.open_map') }} ({{ __('site.a11y.new_window') }})</span>
            </a>
        </div>
    </div>
@endsection
