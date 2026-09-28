{{-- Barre de recherche et filtres d'une liste (GET) --}}
@props(['search' => true, 'placeholder' => 'Rechercher…'])
<form method="GET" class="mb-4 flex flex-wrap items-center gap-2">
    @if ($search)
        <div class="relative min-w-56 flex-1 sm:max-w-sm">
            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ $placeholder }}"
                class="block h-10 w-full rounded-lg border-0 pl-9 pr-3 text-sm shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-brand-500">
        </div>
    @endif
    {{ $slot }}
    <x-button type="submit" variant="outline" size="sm">Filtrer</x-button>
    @if (request()->hasAny(['search', 'status', 'category', 'album']))
        <a href="{{ url()->current() }}" class="text-sm text-gray-500 hover:text-gray-700">Effacer</a>
    @endif
</form>
