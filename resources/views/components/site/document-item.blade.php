@props(['document'])

<li {{ $attributes->class(['group flex items-center gap-4 rounded-xl bg-white p-4 ring-1 ring-brand-900/8 transition hover:ring-gold-300 sm:p-5']) }}>
    <span @class([
        'flex size-12 shrink-0 items-center justify-center rounded-lg text-2xl',
        'bg-red-50 text-red-700' => $document->extension() === 'pdf',
        'bg-brand-50 text-brand-500' => $document->extension() !== 'pdf',
    ]) aria-hidden="true">
        <i class="ph {{ $document->icon() }}"></i>
    </span>
    <div class="min-w-0 flex-1">
        <p class="font-medium leading-snug text-brand-700">{{ $document->t('title') }}</p>
        <p class="mt-1 text-xs tracking-wide text-slate-500 uppercase">
            {{ $document->extension() ?: __('site.documents.file') }}@if ($document->humanSize()) · {{ $document->humanSize() }}@endif
        </p>
    </div>
    <a href="{{ route('site.documents.download', $document) }}"
       class="site-btn site-btn-outline shrink-0 px-3 py-2 sm:px-4"
       aria-label="{{ __('site.documents.download_named', ['title' => $document->t('title')]) }}">
        <i class="ph-bold ph-download-simple" aria-hidden="true"></i>
        <span class="hidden sm:inline">{{ __('site.documents.download') }}</span>
    </a>
</li>
