@props([
    'title' => '',
    'value' => '',
    'icon' => '',
    'color' => 'blue',
    'percentage' => 0,
])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
            @if ($percentage > 0)
                <p class="text-sm text-green-600 flex items-center mt-1">
                    <i class="ph ph-trend-up mr-1"></i>
                    +{{ $percentage }}% ce mois
                </p>
            @elseif ($percentage < 0)
                <p class="text-sm text-red-600 flex items-center mt-1">
                    <i class="ph ph-trend-down mr-1"></i>
                    {{ $percentage }}% ce mois
                </p>
            @endif
        </div>
        <div class="w-12 h-12 bg-{{ $color }}-100 rounded-lg flex items-center justify-center">
            <i class="ph ph-{{ $icon }} text-{{ $color }}-600 text-xl"></i>
        </div>
    </div>
</div>