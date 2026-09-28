{{-- Un bouton qui ouvre une petite fenêtre avec un motif, puis envoie le formulaire --}}
@props(['action', 'method' => 'PUT', 'title', 'label', 'variant' => 'danger', 'field' => 'reason', 'fieldLabel' => 'Motif', 'required' => true, 'size' => 'sm'])
<div x-data="{ open: false }" class="inline-block">
    <x-button type="button" :variant="$variant" :size="$size" @click="open = true">{{ $label }}</x-button>
    <x-modal show="open" :title="$title">
        <form method="POST" action="{{ $action }}" class="space-y-4">
            @csrf
            @method($method)
            {{ $slot }}
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ $fieldLabel }}</label>
                <textarea name="{{ $field }}" rows="3" {{ $required ? 'required' : '' }} class="mt-1 block w-full rounded-lg border-0 py-2 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-brand-500 text-sm"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <x-button type="button" variant="ghost" @click="open = false">Annuler</x-button>
                <x-button type="submit" :variant="$variant">{{ $label }}</x-button>
            </div>
        </form>
    </x-modal>
</div>
