@extends('layouts.site')

@section('title', __('site.services.title'))
@section('description', __('site.services.intro'))

@section('content')
    <x-site.page-hero :title="__('site.services.title')" :eyebrow="__('site.services.eyebrow')" :intro="__('site.services.intro')" :image="asset('images/site/bureaux-4.jpg')" />

    <section class="py-14 sm:py-20">
        <div class="site-container">
            @if ($services->isEmpty())
                <x-site.empty-state icon="ph-identification-card" :title="__('site.services.empty_title')" :text="__('site.services.empty_text')">
                    <a href="{{ route('site.contact') }}" class="site-btn site-btn-navy">{{ __('site.cta.write') }}</a>
                </x-site.empty-state>
            @else
                <h2 class="sr-only">{{ __('site.services.list') }}</h2>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($services as $service)
                        <x-site.service-card :service="$service" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Comment se déroule une démarche --}}
    <section class="bg-paper py-16 sm:py-20" aria-labelledby="steps-title">
        <div class="site-container">
            <x-site.section-heading id="steps-title" center :eyebrow="__('site.services.steps_eyebrow')" :title="__('site.services.steps_title')" />
            <ol class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach (['choose', 'book', 'visit'] as $step)
                    <li class="relative site-card p-7">
                        <span class="flex size-11 items-center justify-center rounded-full bg-brand-700 font-display text-xl font-semibold text-gold-300" aria-hidden="true">{{ $loop->iteration }}</span>
                        <h3 class="mt-5 font-display text-2xl font-semibold text-brand-700">{{ __("site.services.steps.{$step}.title") }}</h3>
                        <p class="mt-2 text-[15px] leading-relaxed text-slate-600">{{ __("site.services.steps.{$step}.text") }}</p>
                    </li>
                @endforeach
            </ol>
            <div class="mt-12 text-center">
                <a href="{{ route('site.appointment') }}" class="site-btn site-btn-gold px-7"><i class="ph-bold ph-calendar-check" aria-hidden="true"></i>{{ __('site.cta.appointment') }}</a>
            </div>
        </div>
    </section>
@endsection
