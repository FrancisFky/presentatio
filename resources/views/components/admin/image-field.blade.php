{{-- Photo : aperçu de l'actuelle, envoi d'une nouvelle, ou suppression --}}
@props(['name' => 'image', 'label' => 'Image', 'path' => null, 'help' => null, 'removable' => true])
@php $error = $errors->first($name); @endphp
<div class="space-y-2" x-data="{ preview: null }">
    <span class="block text-sm font-medium text-gray-900">{{ $label }}</span>
    <div class="flex items-start gap-4">
        <div class="flex h-24 w-32 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-gray-100 ring-1 ring-gray-200">
            <template x-if="preview"><img :src="preview" class="h-full w-full object-cover" alt=""></template>
            <template x-if="!preview">
                @if ($path)
                    <img src="{{ Storage::disk('public')->url($path) }}" class="h-full w-full object-cover" alt="">
                @else
                    <i class="ph ph-image text-3xl text-gray-300"></i>
                @endif
            </template>
        </div>
        <div class="min-w-0 flex-1 space-y-2">
            <input type="file" name="{{ $name }}" accept="image/jpeg,image/png,image/webp"
                @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-600 hover:file:bg-brand-100">
            <p class="text-xs text-gray-500">{{ $help ?? 'JPG, PNG ou WebP, 8 Mo maximum. Convertie en WebP et redimensionnée.' }}</p>
            @if ($path && $removable)
                <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="remove_{{ $name }}" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500"> Retirer l'image
                </label>
            @endif
            @if ($error)<p class="text-sm text-red-600">{{ $error }}</p>@endif
        </div>
    </div>
</div>
