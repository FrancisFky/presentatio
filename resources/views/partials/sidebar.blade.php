@props(['current' => 'dashboard'])

@php
    $admin = auth('admin')->user();
    $pendingAppointments = \App\Models\Appointment::pending()->count();
    $unreadMessages = \App\Models\Message::unread()->count();

    $sections = [
        '' => [
            ['name' => 'Tableau de bord', 'route' => 'dashboard', 'icon' => 'ph-gauge', 'slug' => 'dashboard'],
        ],
        'Demandes' => [
            ['name' => 'Rendez-vous', 'route' => 'appointments.index', 'icon' => 'ph-calendar-check', 'slug' => 'appointments', 'badge' => $pendingAppointments],
            ['name' => 'Messages', 'route' => 'messages.index', 'icon' => 'ph-envelope-simple', 'slug' => 'messages', 'badge' => $unreadMessages],
        ],
        'Contenu' => [
            ['name' => 'Actualités', 'route' => 'news.index', 'icon' => 'ph-newspaper', 'slug' => 'news'],
            ['name' => 'Communiqués', 'route' => 'announcements.index', 'icon' => 'ph-megaphone', 'slug' => 'announcements'],
            ['name' => 'Événements', 'route' => 'events.index', 'icon' => 'ph-calendar-star', 'slug' => 'events'],
            ['name' => 'Services consulaires', 'route' => 'services.index', 'icon' => 'ph-identification-card', 'slug' => 'services'],
            ['name' => 'Documents', 'route' => 'documents.index', 'icon' => 'ph-files', 'slug' => 'documents'],
            ['name' => 'Galerie', 'route' => 'albums.index', 'icon' => 'ph-images', 'slug' => 'gallery'],
            ['name' => 'Jours fériés', 'route' => 'holidays.index', 'icon' => 'ph-calendar-x', 'slug' => 'holidays'],
            ['name' => 'Pages', 'route' => 'pages.index', 'icon' => 'ph-article', 'slug' => 'pages'],
        ],
        'Site' => [
            ['name' => "Page d'accueil", 'route' => ['settings.edit', 'home'], 'icon' => 'ph-house', 'slug' => 'settings-home'],
            ['name' => 'Ambassadeur', 'route' => ['settings.edit', 'ambassador'], 'icon' => 'ph-user-focus', 'slug' => 'settings-ambassador'],
            ['name' => "Contacts d'urgence", 'route' => ['settings.edit', 'contacts'], 'icon' => 'ph-first-aid-kit', 'slug' => 'settings-contacts'],
            ['name' => 'Paramètres', 'route' => ['settings.edit', 'site'], 'icon' => 'ph-sliders', 'slug' => 'settings-site', 'visible' => $admin?->canManageAdmins()],
        ],
        'Équipe' => [
            ['name' => 'Administrateurs', 'route' => 'admins.index', 'icon' => 'ph-user-circle-gear', 'slug' => 'admins-index', 'visible' => $admin?->canManageAdmins()],
            ['name' => 'Journal des actions', 'route' => 'admins.audit', 'icon' => 'ph-list-checks', 'slug' => 'admins-audit', 'visible' => $admin?->canManageAdmins()],
            ['name' => 'Mon profil', 'route' => 'profile', 'icon' => 'ph-user-circle', 'slug' => 'profile'],
        ],
    ];
@endphp

<aside class="fixed top-0 left-0 z-50 flex h-screen w-64 flex-col admin-sidebar text-white transition-transform duration-200 -translate-x-full lg:translate-x-0"
       :class="{ '!translate-x-0': sidebar }">
    <div class="flag-stripe h-1 w-full shrink-0"></div>
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-4 shrink-0">
        <img src="{{ asset('images/armoiries.svg') }}" alt="" class="h-10 w-10 object-contain">
        <span class="leading-tight">
            <span class="block font-display text-lg font-semibold">Ambassade</span>
            <span class="block text-[11px] uppercase tracking-widest text-gold-300">Congo · Kenya</span>
        </span>
    </a>

    <nav class="flex-1 overflow-y-auto px-2 pb-6 space-y-5 [scrollbar-width:none]">
        @foreach ($sections as $title => $links)
            @php $links = array_filter($links, fn ($link) => $link['visible'] ?? true); @endphp
            @continue(empty($links))
            <div>
                @if ($title)
                    <p class="px-2 pb-1 text-[11px] font-semibold uppercase tracking-wider text-white/40">{{ $title }}</p>
                @endif
                <div class="space-y-0.5">
                    @foreach ($links as $link)
                        @php
                            $isActive = $current === $link['slug'];
                            $url = is_array($link['route']) ? route($link['route'][0], $link['route'][1]) : route($link['route']);
                        @endphp
                        <a href="{{ $url }}"
                           class="flex items-center rounded-lg px-3 py-2 text-sm transition {{ $isActive ? 'is-active' : 'text-white/80' }}"
                           @if ($isActive) aria-current="page" @endif>
                            <i class="ph {{ $link['icon'] }} mr-3 text-lg"></i>
                            <span class="flex-1 truncate">{{ $link['name'] }}</span>
                            @if (!empty($link['badge']))
                                <span class="ml-2 rounded-full bg-gold-400 px-2 py-0.5 text-xs font-semibold text-brand-900">{{ $link['badge'] }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>
</aside>
