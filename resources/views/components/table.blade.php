@props([
    'title' => '',
    'subtitle' => '',
    'data' => [],
    'columns' => [], // Can be strings, arrays, or closures
    'headers' => [], // Column headers
    'item_link' => null, // String or Closure
    'show_order' => false, // Show row number
    'show_footer' => false, // Show footer
    'footer_link' => null, // Footer link
])

<div class="container mx-auto">
    <div class="overflow-x-auto bg-white shadow rounded-lg p-4">

        {{-- Table title and subtitle --}}
        @if ($title || $subtitle)
            <div class="mb-4">
                @if ($title)
                    <h3 class="text-lg font-semibold">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="text-sm text-gray-500">{{ $subtitle }}</p>
                @endif
            </div>
        @endif

        <table class="admin-table min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                <tr>
                    @if ($show_order)
                        <th class="px-6 py-2 w-1/12">#</th>
                    @endif
                    @foreach ($headers as $header)
                        <th class="px-6 py-2">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                @forelse ($data as $item)
                    <tr>
                        @if ($show_order)
                            <td class="px-6 py-2 font-medium">{{ $loop->iteration }}.</td>
                        @endif

                        @foreach ($columns as $column)
                            <td class="px-6 py-2">
                                @php
                                    $value = match (true) {
                                        is_array($column) => collect($column)
                                            ->map(fn($field) => data_get($item, $field))
                                            ->filter()
                                            ->join(' '),
                                        is_callable($column) => $column($item),
                                        default => data_get($item, $column),
                                    };

                                    $link = is_callable($item_link)
                                        ? $item_link($item)
                                        : ($item_link
                                            ? $item_link . '/' . ($item->slug ?? $item->id)
                                            : null);
                                @endphp

                                @if ($link)
                                    <a href="{{ $link }}" class="text-brand-700 font-medium hover:underline">
                                        {{ $value }}
                                    </a>
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) + ($show_order ? 1 : 0) }}"
                            class="px-6 py-4 text-center text-gray-500">
                            No data found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($show_footer)
            <div class="mt-4">
                @if ($footer_link)
                    <a href="{{ $footer_link }}" class="text-sm text-gray-500 hover:text-brand-700 flex items-center gap-2">
                        Voir tout <i class="ph ph-arrow-right"></i>
                    </a>
                @endif
            </div>
        @endif

    </div>
</div>
