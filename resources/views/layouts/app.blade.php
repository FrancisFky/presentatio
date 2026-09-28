<!DOCTYPE html>
<html lang="fr" class="h-full admin-page">
<head>
    @include('partials.admin-head')
</head>
<body class="admin-page text-gray-900" x-data="{ sidebar: false }">
    @include('partials.navigation')

    @php $current = $current ?? 'dashboard'; @endphp
    @include('partials.sidebar', ['current' => $current])

    {{-- Fond sombre derrière le menu ouvert sur mobile --}}
    <div x-show="sidebar" x-cloak @click="sidebar = false" class="fixed inset-0 z-40 bg-black/40 lg:hidden"></div>

    <main class="min-h-screen p-4 sm:p-6 lg:ml-64">
        <div class="mx-auto w-full max-w-[1600px]">
            @include('partials.notifications')
            @yield('content')
        </div>
    </main>

    @stack('scripts')
    @livewireScripts
</body>
</html>
