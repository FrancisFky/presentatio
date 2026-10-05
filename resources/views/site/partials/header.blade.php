@use('App\Support\Locales')
@use('App\Support\Site')
@php
    $locale = app()->getLocale();
    // Sur un 404, le paramètre est encore la chaîne brute et non la page
    $currentPage = request()->route('page');
    $pageSlug = $currentPage instanceof \App\Models\Page ? $currentPage->slug : null;

    // Le menu en un seul endroit : l'en-tête bureau et le panneau mobile le lisent tous les deux
    $nav = [
        // « Accueil » : le logo y mène déjà ; masqué du menu bureau tant que la place manque
        ['label' => __('site.nav.home'), 'url' => route('site.home'), 'active' => request()->routeIs('site.home'), 'desktop' => 'hidden 2xl:block'],
        ['label' => __('site.nav.about'), 'children' => [
            ['label' => __('site.nav.about_congo'), 'url' => route('site.page', 'about-congo'), 'active' => $pageSlug === 'about-congo', 'icon' => 'ph-globe-hemisphere-east'],
            ['label' => __('site.nav.about_embassy'), 'url' => route('site.page', 'about-embassy'), 'active' => $pageSlug === 'about-embassy', 'icon' => 'ph-bank'],
            ['label' => __('site.nav.invest'), 'url' => route('site.page', 'invest-in-congo'), 'active' => $pageSlug === 'invest-in-congo', 'icon' => 'ph-chart-line-up'],
            ...(Site::ambassadorPublished() ? [['label' => __('site.nav.ambassador'), 'url' => route('site.ambassador'), 'active' => request()->routeIs('site.ambassador'), 'icon' => 'ph-quotes']] : []),
        ]],
        ['label' => __('site.nav.services'), 'url' => route('site.services.index'), 'active' => request()->routeIs('site.services.*')],
        ['label' => __('site.nav.news'), 'children' => [
            ['label' => __('site.nav.news_all'), 'url' => route('site.news.index'), 'active' => request()->routeIs('site.news.*'), 'icon' => 'ph-newspaper'],
            ['label' => __('site.nav.announcements'), 'url' => route('site.announcements.index'), 'active' => request()->routeIs('site.announcements.*'), 'icon' => 'ph-megaphone'],
        ]],
        ['label' => __('site.nav.events'), 'url' => route('site.events.index'), 'active' => request()->routeIs('site.events.*')],
        ['label' => __('site.nav.gallery'), 'url' => route('site.gallery.index'), 'active' => request()->routeIs('site.gallery.*')],
        ['label' => __('site.nav.documents'), 'url' => route('site.documents.index'), 'active' => request()->routeIs('site.documents.*')],
        ['label' => __('site.nav.contact'), 'url' => route('site.contact'), 'active' => request()->routeIs('site.contact')],
    ];
    $phones = Site::phones();
@endphp

{{-- Barre d'informations pratiques (bureau uniquement) --}}
<div class="hidden bg-brand-900 text-[13px] text-brand-100 lg:block print:hidden">
    <div class="site-container flex h-10 items-center justify-between gap-6">
        <ul class="flex items-center gap-6">
            <li>
                <a href="{{ Site::tel($phones[0]) }}" class="inline-flex items-center gap-2 hover:text-white">
                    <i class="ph ph-phone text-gold-400" aria-hidden="true"></i>{{ $phones[0] }}
                </a>
            </li>
            <li>
                <a href="mailto:{{ Site::email() }}" class="inline-flex items-center gap-2 hover:text-white">
                    <i class="ph ph-envelope-simple text-gold-400" aria-hidden="true"></i>{{ Site::email() }}
                </a>
            </li>
            <li class="hidden items-center gap-2 xl:inline-flex">
                <i class="ph ph-clock text-gold-400" aria-hidden="true"></i>{{ Site::hours() }}
            </li>
        </ul>
        <div class="flex items-center gap-5">
            <a href="{{ route('site.contact') }}#urgence" class="inline-flex items-center gap-2 font-medium text-white hover:text-gold-300">
                <span class="relative flex size-2" aria-hidden="true">
                    <span class="absolute inline-flex size-full rounded-full bg-flag-red opacity-60 motion-safe:animate-ping"></span>
                    <span class="relative inline-flex size-2 rounded-full bg-flag-red"></span>
                </span>
                {{ __('site.emergency.short') }}
            </a>
            @include('site.partials.lang-switch', ['variant' => 'dark'])
        </div>
    </div>
</div>

<header x-data="{ scrolled: false, menu: false }"
        x-init="scrolled = window.scrollY > 8"
        @scroll.window.passive="scrolled = window.scrollY > 8"
        @keydown.escape.window="menu = false"
        :class="scrolled ? 'shadow-[0_6px_24px_-12px_rgb(17_34_51/0.35)]' : ''"
        class="sticky top-0 z-40 border-b border-brand-900/5 bg-white transition-shadow duration-300 print:static print:shadow-none">
    <div class="site-container flex h-18 items-center justify-between gap-4 lg:h-20">
        {{-- Armoiries et nom --}}
        <a href="{{ route('site.home') }}" class="group flex min-w-0 items-center gap-3 xl:shrink-0 rounded-md focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-500">
            <img src="{{ Site::logo() }}" alt="" width="56" height="56" class="size-11 shrink-0 object-contain lg:size-14">
            <span class="min-w-0 leading-tight">
                <span class="block text-[10px] font-semibold tracking-[0.24em] text-gold-600 uppercase sm:text-[11px]">{{ __('site.brand.eyebrow') }}</span>
                <span class="sr-only">— {{ Site::name() }}</span>
            </span>
        </a>

        {{-- Menu principal (grand écran) --}}
        <nav aria-label="{{ __('site.a11y.main_nav') }}" class="hidden xl:block">
            <ul class="flex items-center gap-0.5">
                @foreach ($nav as $item)
                    @if (isset($item['children']))
                        @php $childActive = collect($item['children'])->contains('active', true); @endphp
                        <li class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false; $refs.toggle.focus()">
                            <button type="button" x-ref="toggle" @click="open = ! open" :aria-expanded="open.toString()" aria-expanded="false"
                                    aria-controls="menu-{{ $loop->index }}"
                                    @class([
                                        'inline-flex items-center gap-1 rounded-md px-2.5 py-2 text-[14px] whitespace-nowrap font-medium transition hover:text-brand-500 focus-visible:outline-2 focus-visible:outline-brand-500',
                                        'text-brand-600' => $childActive,
                                        'text-slate-600' => ! $childActive,
                                    ])>
                                {{ $item['label'] }}
                                <i class="ph ph-caret-down text-xs transition" :class="open && 'rotate-180'" aria-hidden="true"></i>
                                @if ($childActive)<span class="absolute inset-x-2.5 -bottom-[1px] h-0.5 rounded-full bg-gold-400" aria-hidden="true"></span>@endif
                            </button>
                            <div id="menu-{{ $loop->index }}" x-cloak x-show="open"
                                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                 class="absolute left-0 top-full z-50 mt-2 w-72 overflow-hidden rounded-xl bg-white p-2 shadow-xl ring-1 ring-brand-900/10">
                                <ul>
                                    @foreach ($item['children'] as $child)
                                        <li>
                                            <a href="{{ $child['url'] }}" @if ($child['active']) aria-current="page" @endif
                                               @class([
                                                   'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition hover:bg-paper focus-visible:bg-paper focus-visible:outline-none',
                                                   'bg-paper font-semibold text-brand-600' => $child['active'],
                                                   'text-slate-700' => ! $child['active'],
                                               ])>
                                                <span class="flex size-8 items-center justify-center rounded-md bg-brand-50 text-brand-500"><i class="ph {{ $child['icon'] }}" aria-hidden="true"></i></span>
                                                {{ $child['label'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @else
                        <li class="relative {{ $item['desktop'] ?? '' }}">
                            <a href="{{ $item['url'] }}" @if ($item['active']) aria-current="page" @endif
                               @class([
                                   'inline-flex rounded-md px-2.5 py-2 text-[14px] whitespace-nowrap font-medium transition hover:text-brand-500 focus-visible:outline-2 focus-visible:outline-brand-500',
                                   'text-brand-600' => $item['active'],
                                   'text-slate-600' => ! $item['active'],
                               ])>{{ $item['label'] }}</a>
                            @if ($item['active'])<span class="absolute inset-x-2.5 -bottom-[1px] h-0.5 rounded-full bg-gold-400" aria-hidden="true"></span>@endif
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>

        <div class="flex shrink-0 items-center gap-2">
            <a href="{{ route('site.appointment') }}" class="site-btn site-btn-gold hidden px-4 whitespace-nowrap sm:inline-flex" @if (request()->routeIs('site.appointment')) aria-current="page" @endif>
                <i class="ph-bold ph-calendar-check" aria-hidden="true"></i>{{ __('site.cta.appointment') }}
            </a>
            <button type="button" @click="menu = true" class="inline-flex size-11 items-center justify-center rounded-md text-brand-600 ring-1 ring-brand-100 hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-brand-500 xl:hidden"
                    aria-controls="menu-mobile" :aria-expanded="menu.toString()" aria-expanded="false">
                <i class="ph ph-list text-2xl" aria-hidden="true"></i>
                <span class="sr-only">{{ __('site.a11y.open_menu') }}</span>
            </button>
        </div>
    </div>

    {{-- Panneau mobile --}}
    <div x-cloak x-show="menu" class="fixed inset-0 z-50 xl:hidden" role="dialog" aria-modal="true" aria-label="{{ __('site.a11y.main_nav') }}" id="menu-mobile">
        <div x-show="menu" x-transition.opacity class="absolute inset-0 bg-brand-950/60 backdrop-blur-sm" @click="menu = false" aria-hidden="true"></div>
        <div x-show="menu" x-trap.inert.noscroll="menu"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
             class="absolute inset-y-0 right-0 flex w-full max-w-sm flex-col bg-white shadow-2xl">
            <div class="flag-stripe h-1" aria-hidden="true"></div>
            <div class="flex items-center justify-between border-b border-brand-900/5 px-5 py-4">
                <span class="font-display text-xl font-semibold text-brand-700">{{ __('site.nav.menu') }}</span>
                <button type="button" @click="menu = false" class="inline-flex size-10 items-center justify-center rounded-md text-brand-600 hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-brand-500">
                    <i class="ph ph-x text-2xl" aria-hidden="true"></i>
                    <span class="sr-only">{{ __('site.a11y.close_menu') }}</span>
                </button>
            </div>
            <nav aria-label="{{ __('site.a11y.main_nav') }}" class="flex-1 overflow-y-auto px-3 py-4">
                <ul class="space-y-1">
                    @foreach ($nav as $item)
                        @if (isset($item['children']))
                            <li x-data="{ open: {{ collect($item['children'])->contains('active', true) ? 'true' : 'false' }} }">
                                <button type="button" @click="open = ! open" :aria-expanded="open.toString()"
                                        class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-left text-[15px] font-medium text-slate-700 hover:bg-paper focus-visible:outline-2 focus-visible:outline-brand-500">
                                    {{ $item['label'] }}
                                    <i class="ph ph-caret-down transition" :class="open && 'rotate-180'" aria-hidden="true"></i>
                                </button>
                                <ul x-show="open" x-collapse class="ms-3 border-s border-gold-200 ps-2">
                                    @foreach ($item['children'] as $child)
                                        <li>
                                            <a href="{{ $child['url'] }}" @if ($child['active']) aria-current="page" @endif
                                               @class(['block rounded-lg px-3 py-2.5 text-[15px] hover:bg-paper', 'font-semibold text-brand-600' => $child['active'], 'text-slate-600' => ! $child['active']])>
                                                {{ $child['label'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li>
                                <a href="{{ $item['url'] }}" @if ($item['active']) aria-current="page" @endif
                                   @class(['block rounded-lg px-3 py-3 text-[15px] font-medium hover:bg-paper', 'bg-paper text-brand-600' => $item['active'], 'text-slate-700' => ! $item['active']])>
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </nav>
            <div class="space-y-4 border-t border-brand-900/5 bg-paper px-5 py-5">
                <a href="{{ route('site.appointment') }}" class="site-btn site-btn-gold w-full">
                    <i class="ph-bold ph-calendar-check" aria-hidden="true"></i>{{ __('site.cta.appointment') }}
                </a>
                <div class="flex items-center justify-between text-sm">
                    <a href="{{ Site::tel($phones[0]) }}" class="inline-flex items-center gap-2 font-medium text-brand-600">
                        <i class="ph ph-phone text-gold-600" aria-hidden="true"></i>{{ $phones[0] }}
                    </a>
                    @include('site.partials.lang-switch', ['variant' => 'light'])
                </div>
            </div>
        </div>
    </div>
</header>
