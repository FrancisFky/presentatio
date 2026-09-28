{{-- Pièce jointe (PDF, Word, Excel…) : lien vers l'actuelle, envoi d'une nouvelle, ou suppression --}}
@props(['name' => 'attachment', 'label' => 'Pièce jointe', 'path' => null, 'accept' => '.pdf', 'help' => null, 'removable' => true, 'required' => false])
@php $error = $errors->first($name); @endphp
<div class="space-y-2">
    <span class="block text-sm font-medium text-gray-900">{{ $label }} @if ($required)<span class="text-red-500">*</span>@endif</span>
    @if ($path)
        <a href="{{ Storage::disk('public')->url($path) }}" target="_blank" class="inline-flex items-center gap-2 text-sm text-brand-600 hover:underline">
            <i class="ph ph-paperclip"></i>{{ basename($path) }}
        </a>
    @endif
    <input type="file" name="{{ $name }}" accept="{{ $accept }}" @if ($required && !$path) required @endif
        class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-600 hover:file:bg-brand-100">
    @if ($help)<p class="text-xs text-gray-500">{{ $help }}</p>@endif
    @if ($path && $removable)
        <label class="inline-flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="remove_{{ $name }}" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500"> Retirer le fichier
        </label>
    @endif
    @if ($error)<p class="text-sm text-red-600">{{ $error }}</p>@endif
</div>
