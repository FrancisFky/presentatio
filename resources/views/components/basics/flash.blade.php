@props([
    'show' => false,
    'message' => 'Sauvegardé',
    'variant' => 'success',   // success | warning | error
    'timeout' => 3000,
    'model' => null,          // e.g. 'success' (Livewire prop name)
    'showModel' => null,      // e.g. 'error' (Livewire prop name for show control)
])

@php
    $variants = [
        'success' => [
            'icon' => 'ph ph-check-circle', 'bg' => 'bg-green-50', 'border' => 'border-green-200',
            'text' => 'text-green-700', 'iconColor' => 'text-green-600',
        ],
        'error' => [
            'icon' => 'ph ph-x-circle', 'bg' => 'bg-red-50', 'border' => 'border-red-200',
            'text' => 'text-red-700', 'iconColor' => 'text-red-600',
        ],
        'warning' => [
            'icon' => 'ph ph-warning-circle', 'bg' => 'bg-yellow-50', 'border' => 'border-yellow-200',
            'text' => 'text-yellow-700', 'iconColor' => 'text-yellow-600',
        ],
    ];
    $v = $variants[$variant] ?? $variants['success'];

    // Build the Alpine "show" source
    $alpineShow = $model
        ? "\$wire.entangle('{$model}').live"   // Livewire v3 style
        : ($showModel 
            ? "\$wire.entangle('{$showModel}').live"  // Use showModel for show control
            : \Illuminate\Support\Js::from((bool) $show));
@endphp

<div
    x-data="{
        show: {{ $alpineShow }},
        timeout: {{ (int) $timeout }},
        timeoutId: null,
        arm() {
            // Clear any existing timeout
            if (this.timeoutId) {
                clearTimeout(this.timeoutId);
            }
            
            // schedule hide each time show becomes true
            if (this.show) {
                this.timeoutId = setTimeout(() => {
                    @if ($model)
                        // For model-based flash, reset the server property
                        $wire.set('{{ $model }}', false);
                    @elseif ($showModel)
                        // For showModel-based flash, reset the show control property
                        $wire.set('{{ $showModel }}', false);
                    @else
                        // For static show-based flash, just hide locally
                        this.show = false;
                    @endif
                    this.timeoutId = null;
                }, this.timeout);
            }
        }
    }"
    x-init="arm(); $watch('show', (value) => { if (value) arm() })"
    x-show="show"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-95"
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-95"
    role="alert"
    class="flex items-center gap-2 px-2 py-1 rounded-md border {{ $v['bg'] }} {{ $v['border'] }}"
>
    <i class="{{ $v['icon'] }} {{ $v['iconColor'] }} text-sm"></i>
    <span class="text-xs font-medium {{ $v['text'] }}">{{ $message }}</span>
</div>
