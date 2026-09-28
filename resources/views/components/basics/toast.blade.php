<div
    x-data="{ show: true }"
    x-show="show"
    x-transition
    x-init="setTimeout(() => show = false, 5000)"
    wire:key="toast-{{ md5(json_encode($errors->all() ?? [])) }}-{{ session('success') ?? '' }}-{{ session('error') ?? '' }}-{{ session('warning') ?? '' }}"
    class="fixed top-4 right-4 z-[9999] max-w-sm w-full pointer-events-auto"
    x-cloak
>
    {{-- Validation Errors --}}
    @isset($errors)
        @if ($errors->any())
            <div class="mb-2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow relative flex items-start space-x-2">
                <i class="ph ph-warning-circle text-xl mt-1"></i>
                <div>
                    <strong class="font-bold">Erreur!</strong>
                    <ul class="mt-1 list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-xl leading-none">
                    &times;
                </button>
            </div>
        @endif        
    @endisset

    {{-- Success Message --}}
    @if (session('success'))
        <div class="mb-2 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow relative flex items-start space-x-2">
            <i class="ph ph-check-circle text-xl mt-1"></i>
            <div>
                <strong class="font-bold">Succès!</strong>
                <span class="block text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="absolute top-2 right-2 text-green-500 hover:text-green-700 text-xl leading-none">
                &times;
            </button>
        </div>
    @endif

    {{-- Error Message --}}
    @if (session('error'))
        <div class="mb-2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow relative flex items-start space-x-2">
            <i class="ph ph-x-circle text-xl mt-1"></i>
            <div>
                <strong class="font-bold">Erreur!</strong>
                <span class="block text-sm">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-xl leading-none">
                &times;
            </button>
        </div>
    @endif

    {{-- Warning Message --}}
    @if (session('warning'))
        <div class="mb-2 bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded shadow relative flex items-start space-x-2">
            <i class="ph ph-warning text-xl mt-1"></i>
            <div>
                <strong class="font-bold">Attention!</strong>
                <span class="block text-sm">{{ session('warning') }}</span>
            </div>
            <button @click="show = false" class="absolute top-2 right-2 text-yellow-500 hover:text-yellow-700 text-xl leading-none">
                &times;
            </button>
        </div>
    @endif
</div>
