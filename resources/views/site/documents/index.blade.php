@extends('layouts.site')

@section('title', __('site.documents.title'))
@section('description', __('site.documents.intro'))

@section('content')
    <x-site.page-hero :title="__('site.documents.title')" :eyebrow="__('site.documents.eyebrow')" :intro="__('site.documents.intro')" />

    <section class="py-14 sm:py-20" x-data="{ search: '', matches(title) { return ! this.search || title.includes(this.search.trim().toLowerCase()) } }">
        <div class="site-container max-w-5xl">
            @if ($groups->isEmpty())
                <x-site.empty-state icon="ph-folder-open" :title="__('site.documents.empty_title')" :text="__('site.documents.empty_text')" />
            @else
                {{-- Filtre instantané sur le titre, côté navigateur : la liste reste courte --}}
                <div class="relative max-w-md">
                    <label for="document-search" class="sr-only">{{ __('site.documents.search') }}</label>
                    <i class="ph ph-magnifying-glass pointer-events-none absolute top-1/2 left-3.5 -translate-y-1/2 text-slate-400" aria-hidden="true"></i>
                    <input id="document-search" type="search" x-model="search" placeholder="{{ __('site.documents.search') }}" class="site-input ps-10">
                </div>

                <div class="mt-10 space-y-12">
                    @foreach ($groups as $category => $documents)
                        <section aria-labelledby="cat-{{ $loop->index }}"
                                 x-show="{{ Js::from($documents->map(fn ($d) => mb_strtolower((string) $d->t('title')))->values()) }}.some(t => matches(t))">
                            <h2 id="cat-{{ $loop->index }}" class="flex items-center gap-3 font-display text-2xl font-semibold text-brand-700 sm:text-3xl">
                                <i class="ph ph-folder-simple text-gold-600" aria-hidden="true"></i>{{ $category !== '' ? $category : __('site.documents.uncategorized') }}
                                <span class="text-sm font-normal text-slate-500">({{ $documents->count() }})</span>
                            </h2>
                            <ul class="mt-5 space-y-3">
                                @foreach ($documents as $document)
                                    <x-site.document-item :document="$document" x-show="matches({{ Js::from(mb_strtolower((string) $document->t('title'))) }})" />
                                @endforeach
                            </ul>
                        </section>
                    @endforeach
                </div>
            @endif

            <p class="mt-14 flex items-start gap-3 rounded-xl bg-paper p-5 text-sm text-slate-600">
                <i class="ph ph-info mt-0.5 text-lg text-gold-600" aria-hidden="true"></i>
                <span>{{ __('site.documents.help') }} <a href="{{ route('site.contact') }}" class="font-semibold text-brand-500 underline-offset-4 hover:underline">{{ __('site.nav.contact') }}</a></span>
            </p>
        </div>
    </section>
@endsection
