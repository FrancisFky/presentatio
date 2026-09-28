@extends('layouts.site')

@section('title', __('site.announcements.title'))
@section('description', __('site.announcements.intro'))

@section('content')
    <x-site.page-hero :title="__('site.announcements.title')" :eyebrow="__('site.announcements.eyebrow')" :intro="__('site.announcements.intro')"
        :crumbs="[__('site.news.title') => route('site.news.index')]" />

    <section class="py-14 sm:py-20">
        <div class="site-container max-w-5xl">
            @if ($announcements->isEmpty())
                <x-site.empty-state icon="ph-megaphone" :title="__('site.announcements.empty_title')" :text="__('site.announcements.empty_text')" />
            @else
                <h2 class="sr-only">{{ __('site.announcements.list') }}</h2>
                <ul class="space-y-4">
                    @foreach ($announcements as $announcement)
                        <li>
                            <article @class([
                                'group relative site-card flex flex-col gap-4 p-6 transition hover:shadow-xl sm:flex-row sm:items-start sm:gap-6 sm:p-7',
                                'border-s-4 border-flag-red' => $announcement->priority === 'urgent',
                                'border-s-4 border-gold-400' => $announcement->priority === 'important',
                            ])>
                                <time datetime="{{ $announcement->published_on->toDateString() }}" class="shrink-0 text-sm sm:w-32">
                                    <span class="block font-display text-3xl leading-none font-semibold text-brand-700">{{ $announcement->published_on->format('d') }}</span>
                                    <span class="text-xs tracking-wider text-slate-500 uppercase">{{ $announcement->published_on->translatedFormat('F Y') }}</span>
                                </time>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if ($announcement->priority !== 'normal' || $announcement->is_pinned)
                                            <x-site.priority-badge :priority="$announcement->priority" :pinned="$announcement->is_pinned" />
                                        @endif
                                        @if ($announcement->category)
                                            <span class="text-xs font-medium tracking-wider text-slate-500 uppercase">{{ $announcement->category }}</span>
                                        @endif
                                    </div>
                                    <h3 class="mt-2 font-display text-2xl leading-snug font-semibold text-brand-700">
                                        <a href="{{ route('site.announcements.show', $announcement) }}" class="after:absolute after:inset-0 focus-visible:outline-none group-focus-within:underline decoration-gold-400 underline-offset-4">{{ $announcement->t('title') }}</a>
                                    </h3>
                                    <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-slate-600">{{ \Illuminate\Support\Str::limit(trim(html_entity_decode(strip_tags((string) $announcement->t('body')))), 220) }}</p>
                                    @if ($announcement->expires_on)
                                        <p class="mt-3 inline-flex items-center gap-1.5 text-xs text-slate-500"><i class="ph ph-hourglass" aria-hidden="true"></i>{{ __('site.announcements.valid_until', ['date' => $announcement->expires_on->translatedFormat('j F Y')]) }}</p>
                                    @endif
                                </div>
                                <i class="ph-bold ph-arrow-right hidden self-center text-brand-300 transition group-hover:translate-x-1 group-hover:text-gold-600 sm:block" aria-hidden="true"></i>
                            </article>
                        </li>
                    @endforeach
                </ul>
                {{ $announcements->links('site.partials.pagination') }}
            @endif
        </div>
    </section>
@endsection
