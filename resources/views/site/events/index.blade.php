@extends('layouts.site')

@section('title', __('site.events.title'))
@section('description', __('site.events.intro'))

@section('content')
    <x-site.page-hero :title="__('site.events.title')" :eyebrow="__('site.events.eyebrow')" :intro="__('site.events.intro')" :image="asset('images/site/diaspora.jpg')" />

    <section class="py-14 sm:py-20">
        <div class="site-container">
            <nav aria-label="{{ __('site.events.tabs') }}" class="inline-flex rounded-full bg-paper p-1 ring-1 ring-brand-900/5">
                @foreach (['upcoming' => __('site.events.upcoming'), 'past' => __('site.events.past')] as $key => $label)
                    <a href="{{ route('site.events.index', $key === 'past' ? ['tab' => 'past'] : []) }}" @if ($tab === $key) aria-current="page" @endif
                       @class(['rounded-full px-5 py-2 text-sm font-semibold transition focus-visible:outline-2 focus-visible:outline-brand-500', 'bg-brand-700 text-white shadow' => $tab === $key, 'text-slate-600 hover:text-brand-600' => $tab !== $key])>
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            <h2 class="sr-only">{{ $tab === 'past' ? __('site.events.past') : __('site.events.upcoming') }}</h2>
            @if ($events->isEmpty())
                <x-site.empty-state class="mt-10" icon="ph-calendar-blank"
                    :title="$tab === 'past' ? __('site.events.empty_past_title') : __('site.events.empty_title')"
                    :text="$tab === 'past' ? null : __('site.events.empty_text')" />
            @else
                <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($events as $event)
                        <x-site.event-card :event="$event" :class="$tab === 'past' ? 'opacity-90' : ''" />
                    @endforeach
                </div>
                {{ $events->links('site.partials.pagination') }}
            @endif
        </div>
    </section>
@endsection
