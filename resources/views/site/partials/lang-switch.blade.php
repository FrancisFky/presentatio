{{-- FR | EN : même page dans l'autre langue. $variant : dark (barre du haut) ou light --}}
@use('App\Support\Locales')
@use('App\Support\Site')
@php $dark = ($variant ?? 'light') === 'dark'; @endphp
<nav aria-label="{{ __('site.a11y.language') }}">
    <ul class="flex items-center gap-1 text-xs font-semibold tracking-wider">
        @foreach (Locales::SUPPORTED as $code)
            @if (! $loop->first)
                <li aria-hidden="true" class="{{ $dark ? 'text-brand-400' : 'text-slate-300' }}">|</li>
            @endif
            <li>
                @if ($code === app()->getLocale())
                    <span aria-current="true" class="rounded px-1.5 py-0.5 {{ $dark ? 'text-gold-300' : 'text-brand-600 underline decoration-gold-400 decoration-2 underline-offset-4' }}">
                        <abbr title="{{ Locales::LABELS[$code] }}" class="no-underline">{{ strtoupper($code) }}</abbr>
                    </span>
                @else
                    <a href="{{ Site::localizedUrl($code) }}" hreflang="{{ $code }}" lang="{{ $code }}"
                       class="rounded px-1.5 py-0.5 transition focus-visible:outline-2 focus-visible:outline-gold-400 {{ $dark ? 'text-brand-200 hover:text-white' : 'text-slate-500 hover:text-brand-600' }}">
                        <span aria-hidden="true">{{ strtoupper($code) }}</span>
                        <span class="sr-only">{{ Locales::LABELS[$code] }}</span>
                    </a>
                @endif
            </li>
        @endforeach
    </ul>
</nav>
