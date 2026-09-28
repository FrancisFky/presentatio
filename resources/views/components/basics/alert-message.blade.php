@props([
    'message' => '',
    'variant' => 'success', // success | warning | error
])

{{-- Success Message --}}
@if ($variant == 'success')
    <div
        class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow relative flex items-start space-x-2">
        <i class="ph ph-check-circle text-xl mt-1"></i>
        <div>
            <strong class="font-bold">Succès!</strong>
            <span class="block text-sm">{{ $message }}</span>
        </div>
        <button @click="show = false"
            class="absolute top-2 right-2 text-green-500 hover:text-green-700 text-xl leading-none">
            &times;
        </button>
    </div>
@endif

{{-- Error Message --}}
@if ($variant == 'error')
    <div
        class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow relative flex items-start space-x-2">
        <i class="ph ph-x-circle text-xl mt-1"></i>
        <div>
            <strong class="font-bold">Erreur!</strong>
            <span class="block text-sm">{{ $message }}</span>
        </div>
        <button @click="show = false"
            class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-xl leading-none">
            &times;
        </button>
    </div>
@endif

{{-- Warning Message --}}
@if ($variant == 'warning')
    <div
        class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded shadow relative flex items-start space-x-2">
        <i class="ph ph-warning text-xl mt-1"></i>
        <div>
            <strong class="font-bold">Attention!</strong>
            <span class="block text-sm">{{ $message }}</span>
        </div>
        <button @click="show = false"
            class="absolute top-2 right-2 text-yellow-500 hover:text-yellow-700 text-xl leading-none">
            &times;
        </button>
    </div>
@endif
