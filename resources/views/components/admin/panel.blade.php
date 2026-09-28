{{-- Bloc de formulaire avec titre --}}
@props(['title' => null, 'description' => null])
<section {{ $attributes->merge(['class' => 'rounded-xl border border-gray-200 bg-white shadow-sm']) }}>
    @if ($title)
        <header class="border-b border-gray-100 px-5 py-4">
            <h2 class="font-semibold text-gray-900">{{ $title }}</h2>
            @if ($description)<p class="mt-0.5 text-sm text-gray-500">{{ $description }}</p>@endif
        </header>
    @endif
    <div class="space-y-5 p-5">{{ $slot }}</div>
</section>
