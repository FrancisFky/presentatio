@extends('layouts.site')

@section('title', $category ? $category->t('name') . ' — ' . __('site.news.title') : __('site.news.title'))
@section('description', __('site.news.intro'))

@section('content')
    <x-site.page-hero :title="__('site.news.title')" :eyebrow="__('site.news.eyebrow')" :intro="__('site.news.intro')" :image="asset('images/site/ruto-nguesso.jpg')" />

    <section class="py-14 sm:py-20">
        <div class="site-container">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                @if ($categories->isNotEmpty())
                    <nav aria-label="{{ __('site.news.filter') }}">
                        <ul class="flex flex-wrap gap-2">
                            <li>
                                <a href="{{ route('site.news.index') }}" @if (! $category) aria-current="page" @endif
                                   @class(['inline-flex rounded-full px-4 py-2 text-sm font-medium transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500', 'bg-brand-700 text-white' => ! $category, 'bg-white text-slate-600 ring-1 ring-brand-100 hover:ring-brand-300' => $category])>
                                    {{ __('site.news.all_categories') }}
                                </a>
                            </li>
                            @foreach ($categories as $item)
                                <li>
                                    <a href="{{ route('site.news.index', ['category' => $item->id]) }}" @if ($category?->is($item)) aria-current="page" @endif
                                       @class(['inline-flex rounded-full px-4 py-2 text-sm font-medium transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500', 'bg-brand-700 text-white' => $category?->is($item), 'bg-white text-slate-600 ring-1 ring-brand-100 hover:ring-brand-300' => ! $category?->is($item)])>
                                        {{ $item->t('name') }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                @endif
                <a href="{{ route('site.announcements.index') }}" class="site-link shrink-0 text-sm"><i class="ph ph-megaphone" aria-hidden="true"></i>{{ __('site.news.see_announcements') }}</a>
            </div>

            @if ($news->isEmpty())
                <x-site.empty-state class="mt-12" icon="ph-newspaper" :title="__('site.news.empty_title')" :text="__('site.news.empty_text')" />
            @else
                <h2 class="sr-only">{{ __('site.news.list') }}</h2>
                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($news as $item)
                        <x-site.news-card :news="$item" />
                    @endforeach
                </div>
                {{ $news->links('site.partials.pagination') }}
            @endif
        </div>
    </section>
@endsection
