{{-- Liens de partage simples (sans script tiers ni traceur) --}}
@props(['url', 'title'])

<div {{ $attributes->class(['flex flex-wrap items-center gap-2']) }} x-data="{ copied: false }">
    <span class="me-1 text-xs font-semibold tracking-[0.18em] text-slate-500 uppercase">{{ __('site.actions.share') }}</span>
    @foreach ([
        ['Facebook', 'ph-facebook-logo', 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url)],
        ['X', 'ph-x-logo', 'https://twitter.com/intent/tweet?url=' . urlencode($url) . '&text=' . urlencode($title)],
        ['LinkedIn', 'ph-linkedin-logo', 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($url)],
        ['WhatsApp', 'ph-whatsapp-logo', 'https://wa.me/?text=' . urlencode($title . ' ' . $url)],
    ] as [$label, $icon, $href])
        <a href="{{ $href }}" target="_blank" rel="noopener noreferrer"
           class="flex size-9 items-center justify-center rounded-full text-brand-500 ring-1 ring-brand-100 transition hover:bg-brand-500 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500">
            <i class="ph {{ $icon }}" aria-hidden="true"></i><span class="sr-only">{{ $label }} ({{ __('site.a11y.new_window') }})</span>
        </a>
    @endforeach
    <button type="button" x-cloak x-show="navigator.clipboard"
            @click="navigator.clipboard.writeText(@js($url)).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
            class="flex h-9 items-center gap-1.5 rounded-full px-3 text-xs font-semibold text-brand-500 ring-1 ring-brand-100 transition hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500">
        <i class="ph" :class="copied ? 'ph-check' : 'ph-link-simple'" aria-hidden="true"></i>
        <span x-text="copied ? @js(__('site.actions.copied')) : @js(__('site.actions.copy_link'))">{{ __('site.actions.copy_link') }}</span>
    </button>
</div>
