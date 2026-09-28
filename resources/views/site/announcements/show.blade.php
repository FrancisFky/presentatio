@extends('layouts.site')
@use('App\Support\Site')

@php
    $title = $announcement->t('title');
    $image = Site::media($announcement->image_path);
@endphp

@section('title', $title)
@section('description', strip_tags((string) $announcement->t('body')))
@section('image', $image ?? '')
@section('og_type', 'article')

@section('content')
    <x-site.page-hero :title="$title" :eyebrow="__('site.announcements.single')" :crumbs="[__('site.announcements.title') => route('site.announcements.index')]">
        <div class="mt-6 flex flex-wrap items-center gap-3 text-sm text-brand-100">
            @if ($announcement->priority !== 'normal' || $announcement->is_pinned)
                <x-site.priority-badge :priority="$announcement->priority" :pinned="$announcement->is_pinned" />
            @endif
            <time datetime="{{ $announcement->published_on->toDateString() }}" class="inline-flex items-center gap-1.5"><i class="ph ph-calendar-blank text-gold-300" aria-hidden="true"></i>{{ $announcement->published_on->translatedFormat('l j F Y') }}</time>
            @if ($announcement->category)
                <span class="inline-flex items-center gap-1.5"><i class="ph ph-tag text-gold-300" aria-hidden="true"></i>{{ $announcement->category }}</span>
            @endif
        </div>
    </x-site.page-hero>

    <div class="site-container grid gap-12 py-14 sm:py-20 lg:grid-cols-12">
        <article class="lg:col-span-8">
            @if ($announcement->priority === 'urgent')
                <p class="mb-8 flex items-start gap-3 rounded-xl bg-red-50 p-4 text-sm text-red-800 ring-1 ring-red-200" role="note">
                    <i class="ph-fill ph-warning-octagon mt-0.5 text-lg" aria-hidden="true"></i>{{ __('site.announcements.urgent_note') }}
                </p>
            @endif
            @if ($image)
                <img src="{{ $image }}" alt="" class="mb-10 w-full rounded-2xl object-cover shadow-lg">
            @endif
            @if ($body = $announcement->t('body'))
                <x-site.prose>{!! $body !!}</x-site.prose>
            @endif
            @if ($announcement->attachment_path)
                <a href="{{ Site::media($announcement->attachment_path) }}" target="_blank" rel="noopener" class="site-btn site-btn-navy mt-10">
                    <i class="ph-bold ph-file-arrow-down" aria-hidden="true"></i>{{ __('site.announcements.attachment') }}
                </a>
            @endif
            @if ($announcement->expires_on)
                <p class="mt-10 inline-flex items-center gap-2 text-sm text-slate-500"><i class="ph ph-hourglass" aria-hidden="true"></i>{{ __('site.announcements.valid_until', ['date' => $announcement->expires_on->translatedFormat('j F Y')]) }}</p>
            @endif
            <div class="mt-10 border-t border-brand-900/10 pt-8">
                <x-site.share :url="route('site.announcements.show', $announcement)" :title="$title" />
            </div>
        </article>

        <aside class="lg:col-span-4" aria-labelledby="others-title">
            <div class="rounded-2xl bg-paper p-6 lg:sticky lg:top-28">
                <h2 id="others-title" class="font-display text-2xl font-semibold text-brand-700">{{ __('site.announcements.others') }}</h2>
                @if ($others->isEmpty())
                    <p class="mt-4 text-sm text-slate-600">{{ __('site.announcements.no_others') }}</p>
                @else
                    <ul class="mt-5 divide-y divide-brand-900/10">
                        @foreach ($others as $other)
                            <li class="py-4 first:pt-0">
                                <a href="{{ route('site.announcements.show', $other) }}" class="group block">
                                    <time datetime="{{ $other->published_on->toDateString() }}" class="text-xs text-gold-700">{{ $other->published_on->translatedFormat('j F Y') }}</time>
                                    <span class="mt-1 block font-medium text-brand-700 group-hover:underline decoration-gold-400 underline-offset-4">{{ $other->t('title') }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <a href="{{ route('site.announcements.index') }}" class="site-link mt-6 text-sm">{{ __('site.announcements.all') }}<i class="ph-bold ph-arrow-right" aria-hidden="true"></i></a>
            </div>
        </aside>
    </div>
@endsection
