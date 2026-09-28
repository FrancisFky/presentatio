@extends('layouts.site')
@use('App\Support\Site')

@php
    $title = $event->t('title');
    $image = Site::media($event->image_path);
    $isPast = $event->starts_on->isBefore(today());
@endphp

@section('title', $title)
@section('description', strip_tags((string) $event->t('description')))
@section('image', $image ?? '')

@section('content')
    <x-site.page-hero :title="$title" :eyebrow="$isPast ? __('site.events.past_single') : __('site.events.single')" :crumbs="[__('site.events.title') => route('site.events.index')]" :image="$image" />

    <div class="site-container grid gap-12 py-14 sm:py-20 lg:grid-cols-12 lg:gap-14">
        <article class="lg:col-span-8">
            @if ($description = $event->t('description'))
                <x-site.prose>{!! $description !!}</x-site.prose>
            @else
                <p class="text-slate-600">{{ __('site.events.no_description') }}</p>
            @endif
            <div class="mt-12 border-t border-brand-900/10 pt-8">
                <x-site.share :url="route('site.events.show', $event)" :title="$title" />
            </div>
        </article>

        <aside class="space-y-6 lg:col-span-4">
            <div class="site-card p-7 lg:sticky lg:top-28">
                <div class="flex items-center gap-4">
                    <x-site.date-badge :date="$event->starts_on" />
                    <div>
                        <p class="font-display text-xl font-semibold text-brand-700 first-letter:uppercase">{{ $event->starts_on->translatedFormat('l j F Y') }}</p>
                        @if ($event->time())
                            <p class="text-sm text-slate-600">{{ __('site.events.at_time', ['time' => $event->time()]) }}</p>
                        @endif
                    </div>
                </div>
                <dl class="mt-6 space-y-4 border-t border-brand-900/10 pt-6 text-sm">
                    @foreach ([
                        ['ph-map-pin', __('site.events.venue'), $event->t('venue')],
                        ['ph-buildings', __('site.events.organizer'), $event->organizer],
                        ['ph-microphone-stage', __('site.events.speaker'), $event->speaker],
                    ] as [$icon, $label, $value])
                        @if ($value)
                            <div class="flex gap-3">
                                <i class="ph {{ $icon }} mt-0.5 text-lg text-gold-600" aria-hidden="true"></i>
                                <div>
                                    <dt class="text-xs font-semibold tracking-[0.16em] text-slate-500 uppercase">{{ $label }}</dt>
                                    <dd class="mt-0.5 text-brand-700">{{ $value }}</dd>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </dl>
                @if ($isPast)
                    <p class="mt-6 rounded-lg bg-paper px-4 py-3 text-sm text-slate-600">{{ __('site.events.is_past') }}</p>
                @elseif ($event->registration_url)
                    <a href="{{ $event->registration_url }}" target="_blank" rel="noopener noreferrer" class="site-btn site-btn-gold mt-6 w-full">
                        <i class="ph-bold ph-ticket" aria-hidden="true"></i>{{ __('site.events.register') }}<span class="sr-only">({{ __('site.a11y.new_window') }})</span>
                    </a>
                @endif
            </div>

            @if ($upcoming->isNotEmpty())
                <div class="rounded-2xl bg-paper p-6">
                    <h2 class="font-display text-xl font-semibold text-brand-700">{{ __('site.events.other_upcoming') }}</h2>
                    <ul class="mt-4 space-y-4">
                        @foreach ($upcoming as $other)
                            <li>
                                <a href="{{ route('site.events.show', $other) }}" class="group flex items-center gap-3">
                                    <span class="w-14 shrink-0 rounded-md bg-white py-1.5 text-center ring-1 ring-brand-900/10">
                                        <span class="block text-[10px] font-semibold text-gold-700 uppercase">{{ $other->starts_on->translatedFormat('M') }}</span>
                                        <span class="block font-display text-xl leading-none font-semibold text-brand-700">{{ $other->starts_on->format('d') }}</span>
                                    </span>
                                    <span class="text-sm font-medium text-brand-700 group-hover:underline decoration-gold-400 underline-offset-4">{{ $other->t('title') }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </aside>
    </div>
@endsection
