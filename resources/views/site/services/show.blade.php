@extends('layouts.site')
@use('App\Models\Service')
@use('App\Support\Site')

@php
    $title = $service->t('title');
    // Icône de chaque rubrique de la fiche, dans l'ordre de Service::TRANSLATED_FIELDS
    $icons = [
        'description' => 'ph-info',
        'requirements' => 'ph-list-checks',
        'documents' => 'ph-files',
        'fees' => 'ph-coins',
        'processing_time' => 'ph-hourglass-medium',
        'office_hours' => 'ph-clock',
    ];
    $sections = collect(array_keys(Service::TRANSLATED_FIELDS))
        ->mapWithKeys(fn ($field) => [$field => $service->t($field)])
        ->filter();
    // Les deux champs courts vont dans l'encadré « En bref » plutôt qu'en rubrique
    $facts = $sections->only(['processing_time', 'office_hours']);
    $sections = $sections->except(['processing_time', 'office_hours']);
@endphp

@section('title', $title)
@section('description', strip_tags((string) $service->t('description')) ?: __('site.services.intro'))

@section('content')
    <x-site.page-hero :title="$title" :eyebrow="__('site.services.single')" :crumbs="[__('site.services.title') => route('site.services.index')]" :image="asset('images/site/bureaux-4.jpg')">
        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('site.appointment', ['service' => $service->id]) }}" class="site-btn site-btn-gold"><i class="ph-bold ph-calendar-check" aria-hidden="true"></i>{{ __('site.services.book') }}</a>
        </div>
    </x-site.page-hero>

    <div class="site-container grid gap-12 py-14 sm:py-20 lg:grid-cols-12 lg:gap-14">
        <div class="lg:col-span-8">
            @if ($sections->isEmpty())
                <x-site.empty-state icon="ph-note-pencil" :title="__('site.services.no_details_title')" :text="__('site.services.no_details_text')" />
            @else
                {{-- Sommaire : utile quand la fiche est longue --}}
                @if ($sections->count() > 2)
                    <nav aria-label="{{ __('site.services.toc') }}" class="mb-10 flex flex-wrap gap-2">
                        @foreach ($sections as $field => $content)
                            <a href="#{{ $field }}" class="inline-flex items-center gap-1.5 rounded-full bg-paper px-3.5 py-1.5 text-sm font-medium text-brand-600 ring-1 ring-gold-200 hover:ring-gold-400">
                                <i class="ph {{ $icons[$field] }} text-gold-600" aria-hidden="true"></i>{{ __("site.services.fields.{$field}") }}
                            </a>
                        @endforeach
                    </nav>
                @endif
                <div class="space-y-12">
                    @foreach ($sections as $field => $content)
                        <section id="{{ $field }}" class="scroll-mt-28" aria-labelledby="{{ $field }}-title">
                            <h2 id="{{ $field }}-title" class="flex items-center gap-3 font-display text-3xl font-semibold text-brand-700">
                                <span class="flex size-10 items-center justify-center rounded-lg bg-brand-700 text-xl text-gold-300" aria-hidden="true"><i class="ph {{ $icons[$field] }}"></i></span>
                                {{ __("site.services.fields.{$field}") }}
                            </h2>
                            <x-site.prose class="mt-5">{!! $content !!}</x-site.prose>
                        </section>
                    @endforeach
                </div>
            @endif
        </div>

        <aside class="space-y-6 lg:col-span-4">
            <div class="overflow-hidden rounded-2xl bg-brand-700 text-white shadow-xl lg:sticky lg:top-28">
                <div class="flag-stripe h-1" aria-hidden="true"></div>
                <div class="p-7">
                    <h2 class="font-display text-2xl font-semibold">{{ __('site.services.at_a_glance') }}</h2>
                    <dl class="mt-5 space-y-4 text-sm">
                        @foreach ($facts as $field => $value)
                            <div class="flex gap-3">
                                <i class="ph {{ $icons[$field] }} mt-0.5 text-lg text-gold-300" aria-hidden="true"></i>
                                <div>
                                    <dt class="text-xs font-semibold tracking-[0.16em] text-gold-200 uppercase">{{ __("site.services.fields.{$field}") }}</dt>
                                    <dd class="mt-0.5 text-white">{{ $value }}</dd>
                                </div>
                            </div>
                        @endforeach
                        <div class="flex gap-3">
                            <i class="ph ph-map-pin mt-0.5 text-lg text-gold-300" aria-hidden="true"></i>
                            <div>
                                <dt class="text-xs font-semibold tracking-[0.16em] text-gold-200 uppercase">{{ __('site.contact.address') }}</dt>
                                <dd class="mt-0.5 whitespace-pre-line text-white">{{ Site::address() }}</dd>
                            </div>
                        </div>
                    </dl>
                    <a href="{{ route('site.appointment', ['service' => $service->id]) }}" class="site-btn site-btn-gold mt-7 w-full"><i class="ph-bold ph-calendar-check" aria-hidden="true"></i>{{ __('site.services.book') }}</a>
                    <a href="{{ route('site.contact') }}" class="mt-3 block text-center text-sm text-brand-100 underline-offset-4 hover:text-white hover:underline">{{ __('site.services.question') }}</a>
                </div>
            </div>

            @if ($others->isNotEmpty())
                <nav aria-labelledby="other-services-title" class="rounded-2xl bg-paper p-6">
                    <h2 id="other-services-title" class="font-display text-xl font-semibold text-brand-700">{{ __('site.services.others') }}</h2>
                    <ul class="mt-4 space-y-1">
                        @foreach ($others as $other)
                            <li>
                                <a href="{{ route('site.services.show', $other) }}" class="flex items-center gap-3 rounded-lg px-2 py-2 text-sm text-slate-700 hover:bg-white hover:text-brand-600">
                                    <i class="ph ph-{{ $other->icon ?: 'file-text' }} text-lg text-gold-600" aria-hidden="true"></i>{{ $other->t('title') }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endif
        </aside>
    </div>
@endsection
