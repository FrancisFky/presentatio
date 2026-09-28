@props([
    'type' => 'info',
    'message' => '',
    'dismissible' => true,
])

@php
    $typeClasses = match($type) {
        'success' => 'bg-green-50 border-green-200 text-green-800',
        'error' => 'bg-red-50 border-red-200 text-red-800',
        'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
        'info' => 'bg-blue-50 border-blue-200 text-blue-800',
        'status' => 'bg-gray-50 border-gray-200 text-gray-800',
        default => 'bg-blue-50 border-blue-200 text-blue-800',
    };
    
    $iconClasses = match($type) {
        'success' => 'ph ph-check-circle text-green-400',
        'error' => 'ph ph-x-circle text-red-400',
        'warning' => 'ph ph-warning text-yellow-400',
        'info' => 'ph ph-info text-blue-400',
        'status' => 'ph ph-info text-gray-400',
        default => 'ph ph-info text-blue-400',
    };
    
    $buttonClasses = match($type) {
        'success' => 'bg-green-50 text-green-500 hover:bg-green-100 focus:ring-green-600 focus:ring-offset-green-50',
        'error' => 'bg-red-50 text-red-500 hover:bg-red-100 focus:ring-red-600 focus:ring-offset-red-50',
        'warning' => 'bg-yellow-50 text-yellow-500 hover:bg-yellow-100 focus:ring-yellow-600 focus:ring-offset-yellow-50',
        'info' => 'bg-blue-50 text-blue-500 hover:bg-blue-100 focus:ring-blue-600 focus:ring-offset-blue-50',
        'status' => 'bg-gray-50 text-gray-500 hover:bg-gray-100 focus:ring-gray-600 focus:ring-offset-gray-50',
        default => 'bg-blue-50 text-blue-500 hover:bg-blue-100 focus:ring-blue-600 focus:ring-offset-blue-50',
    };
@endphp

<div class="mb-4 rounded-md p-4 border {{ $typeClasses }}">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="{{ $iconClasses }} text-xl"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium">
                {{ $message }}
            </p>
        </div>
        @if($dismissible)
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button 
                        type="button" 
                        class="inline-flex rounded-md p-1.5 {{ $buttonClasses }} focus:outline-none focus:ring-2 focus:ring-offset-2" 
                        onclick="this.parentElement.parentElement.parentElement.parentElement.remove()"
                    >
                        <span class="sr-only">Dismiss</span>
                        <i class="ph ph-x text-lg"></i>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div> 