{{-- En-tête de page : titre, fil d'Ariane, bouton d'action --}}
@props(['title', 'items' => [], 'subtitle' => null])
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
    <div class="min-w-0">
        @if ($items)
            <nav class="mb-1 flex flex-wrap items-center gap-1 text-sm text-gray-500" aria-label="Fil d'Ariane">
                @foreach ($items as $label => $url)
                    @if (!$loop->first)<i class="ph ph-caret-right text-xs text-gray-300"></i>@endif
                    @if ($url)<a href="{{ $url }}" class="hover:text-brand-600">{{ $label }}</a>@else<span>{{ $label }}</span>@endif
                @endforeach
            </nav>
        @endif
        <h1 class="font-display text-3xl font-semibold text-brand-700">{{ $title }}</h1>
        @if ($subtitle)<p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>@endif
    </div>
    @isset($action)<div class="flex shrink-0 flex-wrap gap-2">{{ $action }}</div>@endisset
</div>
