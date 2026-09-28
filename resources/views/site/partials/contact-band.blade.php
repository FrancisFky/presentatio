{{-- Bandeau « Nous joindre » : coordonnées, horaires, urgence et appels à l'action --}}
@use('App\Support\Site')
@php
    $phones = Site::phones();
    $emergency = Site::emergency();
@endphp
<section class="relative isolate overflow-hidden bg-brand-900 py-20 sm:py-24 print:hidden" aria-labelledby="contact-band-title">
    <img src="{{ asset('images/site/drapeaux-congo-kenya.jpg') }}" alt="" loading="lazy" class="absolute inset-0 -z-20 size-full object-cover opacity-20">
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-brand-950 via-brand-900/95 to-brand-700/90" aria-hidden="true"></div>

    <div class="site-container grid gap-12 lg:grid-cols-12 lg:gap-10">
        <div class="lg:col-span-5">
            <p class="site-eyebrow site-eyebrow-light">{{ __('site.contact.band_eyebrow') }}</p>
            <h2 id="contact-band-title" class="mt-3 font-display text-3xl font-semibold text-white sm:text-4xl lg:text-5xl">{{ __('site.contact.band_title') }}</h2>
            <p class="mt-5 max-w-md leading-relaxed text-brand-100">{{ __('site.contact.band_intro') }}</p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('site.appointment') }}" class="site-btn site-btn-gold"><i class="ph-bold ph-calendar-check" aria-hidden="true"></i>{{ __('site.cta.appointment') }}</a>
                <a href="{{ route('site.contact') }}" class="site-btn site-btn-light"><i class="ph-bold ph-envelope-simple" aria-hidden="true"></i>{{ __('site.cta.write') }}</a>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:col-span-7">
            <div class="rounded-xl bg-white/5 p-6 ring-1 ring-white/10">
                <i class="ph ph-map-pin text-2xl text-gold-300" aria-hidden="true"></i>
                <h3 class="mt-3 text-xs font-semibold tracking-[0.18em] text-gold-200 uppercase">{{ __('site.contact.address') }}</h3>
                <p class="mt-2 whitespace-pre-line text-white">{{ Site::address() }}</p>
                <a href="{{ Site::mapUrl() }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex items-center gap-1.5 text-sm text-gold-300 underline-offset-4 hover:underline">
                    {{ __('site.contact.open_map') }}<i class="ph ph-arrow-square-out" aria-hidden="true"></i><span class="sr-only">({{ __('site.a11y.new_window') }})</span>
                </a>
            </div>
            <div class="rounded-xl bg-white/5 p-6 ring-1 ring-white/10">
                <i class="ph ph-phone text-2xl text-gold-300" aria-hidden="true"></i>
                <h3 class="mt-3 text-xs font-semibold tracking-[0.18em] text-gold-200 uppercase">{{ __('site.contact.phone_email') }}</h3>
                <ul class="mt-2 space-y-1 text-white">
                    @foreach ($phones as $phone)
                        <li><a href="{{ Site::tel($phone) }}" class="hover:text-gold-200">{{ $phone }}</a></li>
                    @endforeach
                    <li><a href="mailto:{{ Site::email() }}" class="break-all hover:text-gold-200">{{ Site::email() }}</a></li>
                </ul>
            </div>
            <div class="rounded-xl bg-white/5 p-6 ring-1 ring-white/10">
                <i class="ph ph-clock text-2xl text-gold-300" aria-hidden="true"></i>
                <h3 class="mt-3 text-xs font-semibold tracking-[0.18em] text-gold-200 uppercase">{{ __('site.contact.hours') }}</h3>
                <p class="mt-2 whitespace-pre-line text-white">{{ Site::hours() }}</p>
            </div>
            <div class="rounded-xl bg-flag-red/15 p-6 ring-1 ring-flag-red/40">
                <i class="ph-fill ph-siren text-2xl text-red-300" aria-hidden="true"></i>
                <h3 class="mt-3 text-xs font-semibold tracking-[0.18em] text-red-200 uppercase">{{ __('site.emergency.title') }}</h3>
                <a href="{{ Site::tel($emergency['hotline']) }}" class="mt-2 block font-display text-2xl font-semibold text-white hover:text-gold-200">{{ $emergency['hotline'] }}</a>
                @if (! empty($emergency['whatsapp']))
                    <a href="{{ Site::whatsapp($emergency['whatsapp']) }}" target="_blank" rel="noopener noreferrer" class="mt-1 inline-flex items-center gap-1.5 text-sm text-brand-100 hover:text-white">
                        <i class="ph ph-whatsapp-logo" aria-hidden="true"></i>WhatsApp {{ $emergency['whatsapp'] }}
                    </a>
                @endif
                <a href="{{ route('site.contact') }}#urgence" class="mt-2 block text-sm text-red-200 underline-offset-4 hover:underline">{{ __('site.emergency.more') }}</a>
            </div>
        </div>
    </div>
</section>
