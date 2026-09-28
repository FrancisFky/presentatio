@php $admin = Auth::guard('admin')->user(); @endphp
<nav class="sticky top-0 z-30 admin-topbar lg:ml-64">
    <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6">
        <button type="button" class="lg:hidden rounded-lg p-2 text-gray-600 hover:bg-gray-100" @click="sidebar = true" aria-label="Ouvrir le menu">
            <i class="ph ph-list text-2xl"></i>
        </button>

        <a href="{{ url('/') }}" target="_blank" class="hidden sm:inline-flex items-center gap-2 text-sm text-gray-600 hover:text-brand-600">
            <i class="ph ph-arrow-square-out"></i> Voir le site
        </a>

        <div class="relative ml-auto" x-data="{ open: false }">
            <button @click="open = !open" @click.away="open = false"
                class="flex items-center gap-2 rounded-lg p-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-500 text-sm font-medium text-white">
                    {{ mb_strtoupper(mb_substr($admin->name, 0, 1)) }}
                </span>
                <span class="hidden md:block text-left leading-tight">
                    <span class="block font-medium">{{ $admin->name }}</span>
                    <span class="block text-xs text-gray-500">{{ $admin->role_name }}</span>
                </span>
                <i class="ph ph-caret-down text-gray-400 transition-transform" :class="{ 'rotate-180': open }"></i>
            </button>

            <div x-show="open" x-cloak x-transition.origin.top.right
                class="absolute right-0 mt-2 w-56 rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
                <div class="border-b border-gray-100 px-4 py-2">
                    <p class="truncate text-sm font-medium text-gray-900">{{ $admin->name }}</p>
                    <p class="truncate text-xs text-gray-500">{{ $admin->email }}</p>
                </div>
                <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <i class="ph ph-user mr-3 text-gray-400"></i>Mon profil
                </a>
                <div class="my-1 border-t border-gray-100"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <i class="ph ph-sign-out mr-3 text-red-400"></i>Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
