@use('App\Models\Setting')
@use('App\Support\Site')
@php
    $phones = Site::phones();
    $socials = Site::socials();
    $emergency = Site::emergency();
@endphp
<footer class="site-guilloche relative bg-brand-900 text-brand-100 print:hidden" aria-labelledby="footer-title">
    <h2 id="footer-title" class="sr-only">{{ __('site.footer.title') }}</h2>
    <div class="h-px bg-gradient-to-r from-transparent via-gold-400/60 to-transparent" aria-hidden="true"></div>

    <div class="site-container grid gap-12 py-16 sm:grid-cols-2 lg:grid-cols-12 lg:gap-8">
        {{-- Identité --}}
        <div class="lg:col-span-4">
            <a href="{{ route('site.home') }}" class="inline-flex items-center gap-4 rounded-md focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-gold-400">
                <span class="flex size-16 items-center justify-center rounded-full bg-white p-2 ring-4 ring-white/5">
                    <img src="{{ Site::logo() }}" alt="" width="48" height="48" class="size-12 object-contain">
                </span>
                <span class="leading-tight">
                    <span class="block text-[11px] font-semibold tracking-[0.24em] text-gold-400 uppercase">{{ __('site.brand.eyebrow') }}</span>
                    <span class="block font-display text-2xl font-semibold text-white">{{ __('site.brand.title') }}</span>
                </span>
            </a>
            <p class="mt-6 max-w-sm text-sm leading-relaxed text-brand-200">
                {{ Setting::localized('site.footer', default: __('site.footer.about')) }}
            </p>
            @if ($socials)
                <ul class="mt-6 flex flex-wrap gap-2" aria-label="{{ __('site.footer.follow') }}">
                    @foreach ($socials as $social)
                        <li>
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                               class="flex size-10 items-center justify-center rounded-full text-lg text-white ring-1 ring-white/15 transition hover:bg-gold-400 hover:text-brand-900 hover:ring-gold-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold-400">
                                <i class="ph {{ $social['icon'] }}" aria-hidden="true"></i>
                                <span class="sr-only">{{ $social['label'] }} ({{ __('site.a11y.new_window') }})</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Coordonnées --}}
        <div class="lg:col-span-3">
            <h3 class="font-display text-xl font-semibold text-white">{{ __('site.footer.contact') }}</h3>
            <ul class="mt-5 space-y-4 text-sm">
                <li class="flex gap-3">
                    <i class="ph ph-map-pin mt-0.5 text-lg text-gold-400" aria-hidden="true"></i>
                    <span>
                        <span class="sr-only">{{ __('site.contact.address') }} :</span>
                        <span class="whitespace-pre-line">{{ Site::address() }}</span>
                        <a href="{{ Site::mapUrl() }}" target="_blank" rel="noopener noreferrer" class="mt-1 block text-gold-300 underline-offset-4 hover:underline">{{ __('site.contact.open_map') }}</a>
                    </span>
                </li>
                <li class="flex gap-3">
                    <i class="ph ph-phone mt-0.5 text-lg text-gold-400" aria-hidden="true"></i>
                    <span class="flex flex-col">
                        <span class="sr-only">{{ __('site.contact.phone') }} :</span>
                        @foreach ($phones as $phone)
                            <a href="{{ Site::tel($phone) }}" class="hover:text-white">{{ $phone }}</a>
                        @endforeach
                    </span>
                </li>
                <li class="flex gap-3">
                    <i class="ph ph-envelope-simple mt-0.5 text-lg text-gold-400" aria-hidden="true"></i>
                    <a href="mailto:{{ Site::email() }}" class="break-all hover:text-white"><span class="sr-only">{{ __('site.contact.email') }} :</span>{{ Site::email() }}</a>
                </li>
            </ul>
        </div>

        {{-- Liens utiles --}}
        <div class="lg:col-span-2">
            <h3 class="font-display text-xl font-semibold text-white">{{ __('site.footer.links') }}</h3>
            <ul class="mt-5 space-y-2.5 text-sm">
                @foreach ([
                    ['site.services.index', 'site.nav.services'],
                    ['site.appointment', 'site.cta.appointment'],
                    ['site.news.index', 'site.nav.news'],
                    ['site.announcements.index', 'site.nav.announcements'],
                    ['site.events.index', 'site.nav.events'],
                    ['site.documents.index', 'site.nav.documents'],
                    ['site.contact', 'site.nav.contact'],
                ] as [$route, $label])
                    <li><a href="{{ route($route) }}" class="transition hover:text-gold-300">{{ __($label) }}</a></li>
                @endforeach
            </ul>
        </div>

        {{-- Horaires et urgence --}}
        <div class="lg:col-span-3">
            <h3 class="font-display text-xl font-semibold text-white">{{ __('site.contact.hours') }}</h3>
            <p class="mt-5 flex gap-3 text-sm">
                <i class="ph ph-clock mt-0.5 text-lg text-gold-400" aria-hidden="true"></i>
                <span class="whitespace-pre-line">{{ Site::hours() }}</span>
            </p>
            <div class="mt-6 rounded-xl bg-flag-red/10 p-4 ring-1 ring-flag-red/30">
                <p class="flex items-center gap-2 text-xs font-semibold tracking-[0.18em] text-red-200 uppercase">
                    <i class="ph-fill ph-siren text-base text-red-300" aria-hidden="true"></i>{{ __('site.emergency.title') }}
                </p>
                <a href="{{ Site::tel($emergency['hotline']) }}" class="mt-2 block font-display text-2xl font-semibold text-white hover:text-gold-200">{{ $emergency['hotline'] }}</a>
                <p class="mt-1 text-xs text-brand-200">{{ __('site.emergency.footer_note') }}</p>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="site-container flex flex-col gap-3 py-6 text-xs text-brand-300 sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ now()->year }} {{ Site::name() }}. {{ __('site.footer.rights') }}</p>
            <ul class="flex flex-wrap items-center gap-x-5 gap-y-2">
                <li><a href="{{ route('sitemap') }}" class="hover:text-white">{{ __('site.footer.sitemap') }}</a></li>
                <li><a href="{{ route('login') }}" rel="nofollow" class="inline-flex items-center gap-1.5 hover:text-white"><i class="ph ph-lock-simple" aria-hidden="true"></i>{{ __('site.footer.admin') }}</a></li>
            </ul>
        </div>
    </div>
</footer>
