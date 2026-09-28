@extends('layouts.site')
@use('App\Support\Site')

@section('title', __('site.appointment.title'))
@section('description', __('site.appointment.intro'))

@section('content')
    <x-site.page-hero :title="__('site.appointment.title')" :eyebrow="__('site.appointment.eyebrow')" :intro="__('site.appointment.intro')" :image="asset('images/site/bureaux-3.jpg')" class="print:hidden" />

    <div class="bg-paper">
        <div class="site-container grid gap-10 py-14 sm:py-20 lg:grid-cols-12 lg:gap-12">
            <div class="lg:col-span-8">
                <livewire:appointment-form :service="$service" />
            </div>

            <aside class="space-y-6 lg:col-span-4 print:hidden">
                <div class="site-card p-7">
                    <h2 class="font-display text-2xl font-semibold text-brand-700">{{ __('site.appointment.how_title') }}</h2>
                    <ol class="mt-5 space-y-5">
                        @foreach (['request', 'review', 'confirm'] as $step)
                            <li class="flex gap-4">
                                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-brand-700 text-sm font-semibold text-gold-300" aria-hidden="true">{{ $loop->iteration }}</span>
                                <div>
                                    <p class="font-medium text-brand-700">{{ __("site.appointment.how.{$step}.title") }}</p>
                                    <p class="mt-0.5 text-sm leading-relaxed text-slate-600">{{ __("site.appointment.how.{$step}.text") }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>

                <div class="rounded-2xl bg-brand-700 p-7 text-white">
                    <h2 class="flex items-center gap-2 font-display text-2xl font-semibold"><i class="ph ph-clock text-gold-300" aria-hidden="true"></i>{{ __('site.contact.hours') }}</h2>
                    <p class="mt-3 whitespace-pre-line text-brand-100">{{ Site::hours() }}</p>
                    <p class="mt-4 text-sm text-brand-200">{{ __('site.appointment.bring_documents') }}</p>
                </div>

                @if ($holidays->isNotEmpty())
                    <div class="site-card p-7">
                        <h2 class="flex items-center gap-2 font-display text-2xl font-semibold text-brand-700"><i class="ph ph-calendar-x text-gold-600" aria-hidden="true"></i>{{ __('site.holidays.title') }}</h2>
                        <p class="mt-2 text-sm text-slate-600">{{ __('site.holidays.appointment_note') }}</p>
                        <ul class="mt-4 divide-y divide-brand-900/10 text-sm">
                            @foreach ($holidays as $holiday)
                                <li class="flex items-center justify-between gap-4 py-2.5">
                                    <span class="font-medium text-brand-700">{{ $holiday->t('name') }}</span>
                                    <time datetime="{{ $holiday->date->toDateString() }}" class="shrink-0 text-slate-500">{{ $holiday->date->translatedFormat('j M Y') }}</time>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </aside>
        </div>
    </div>
@endsection
