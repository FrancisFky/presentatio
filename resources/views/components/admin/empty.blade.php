@props(['icon' => 'ph-tray', 'title', 'text' => null])
<div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center">
    <i class="ph {{ $icon }} text-4xl text-gray-300"></i>
    <p class="mt-3 font-medium text-gray-700">{{ $title }}</p>
    @if ($text)<p class="mt-1 text-sm text-gray-500">{{ $text }}</p>@endif
    @if (trim($slot))<div class="mt-4">{{ $slot }}</div>@endif
</div>
