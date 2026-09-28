@props(['activity'])

@php
    use App\Models\UserActivity;

    // Map activity types to colors & Phosphor icons
    $typeConfig = [
        UserActivity::TYPE_NEUTRAL        => ['color' => 'gray',   'icon' => 'ph-bell'],
        UserActivity::TYPE_AUTHENTICATION => ['color' => 'blue',   'icon' => 'ph-lock-key-open'],
        UserActivity::TYPE_CRUD           => ['color' => 'green',  'icon' => 'ph-pencil-simple'],
        UserActivity::TYPE_PROFILE        => ['color' => 'purple', 'icon' => 'ph-user-circle'],
        UserActivity::TYPE_NOTIFICATION   => ['color' => 'yellow', 'icon' => 'ph-bell-ringing'],
        UserActivity::TYPE_SYSTEM         => ['color' => 'red',    'icon' => 'ph-cpu'],
        UserActivity::TYPE_SYSTEM_ERROR   => ['color' => 'red',    'icon' => 'ph-bug'],
    ];

    // Determine config for this activity
    $config = $typeConfig[$activity->type] ?? $typeConfig[UserActivity::TYPE_NEUTRAL];
    $color  = $config['color'];
    $icon   = $config['icon'];
@endphp

<div class="flex items-start bg-white p-4 rounded-lg">
    {{-- Icon --}}
    <div class="flex-shrink-0">
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-{{ $color }}-100 text-{{ $color }}-600">
            <i class="ph {{ $icon }} text-xl"></i>
        </span>
    </div>

    {{-- Details --}}
    <div class="ml-4 flex-1">
        <p class="text-sm text-gray-700">{{ $activity->description }}</p>
        <div class="mt-1 flex items-center text-xs text-gray-500">
            <span>{{ $activity->created_at->diffForHumans() }}</span>
        </div>
    </div>
</div>
