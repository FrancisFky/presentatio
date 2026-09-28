@extends('layouts.app')
@section('title', $album->name_fr . ' - Galerie - Admin Ambassade')

@section('content')
<x-admin.header :title="$album->name_fr" :subtitle="$photos->count() . ' photo(s)'"
    :items="['Tableau de bord' => route('dashboard'), 'Galerie' => route('albums.index'), $album->name_fr => null]">
    <x-slot name="action">
        <x-admin.delete-button :action="route('albums.destroy', $album)" size="sm" label="Supprimer l'album" confirm="Supprimer l'album et TOUTES ses photos ?" />
    </x-slot>
</x-admin.header>

<div class="grid gap-6 xl:grid-cols-[1fr_22rem]">
    <div class="space-y-6">
        {{-- Envoi groupé --}}
        <form method="POST" action="{{ route('photos.store', $album) }}" enctype="multipart/form-data"
            x-data="{ count: 0, dragging: false }" class="rounded-xl border-2 border-dashed bg-white p-6 text-center transition"
            :class="dragging ? 'border-brand-400 bg-brand-50' : 'border-gray-300'">
            @csrf
            <label class="block cursor-pointer" @dragover.prevent="dragging = true" @dragleave="dragging = false" @drop="dragging = false">
                <i class="ph ph-upload-simple text-4xl text-gray-400"></i>
                <p class="mt-2 font-medium text-gray-700">Glissez des photos ici ou cliquez pour choisir</p>
                <p class="text-sm text-gray-500">Jusqu'à 30 photos, 8 Mo chacune. Converties en WebP.</p>
                <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp" class="sr-only" @change="count = $event.target.files.length">
            </label>
            <div x-show="count > 0" x-cloak class="mt-4">
                <x-button type="submit"><i class="ph ph-upload-simple mr-2"></i>Envoyer <span x-text="count"></span> photo(s)</x-button>
            </div>
        </form>

        @if ($photos->isEmpty())
            <x-admin.empty icon="ph-image" title="Album vide" />
        @else
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 2xl:grid-cols-4">
                @foreach ($photos as $photo)
                    <div class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm" x-data="{ edit: false, lang: 'fr' }">
                        <div class="relative aspect-[4/3] bg-gray-100">
                            <img src="{{ $photo->url() }}" alt="" class="h-full w-full object-cover" loading="lazy">
                            <form method="POST" action="{{ route('photos.feature', $photo) }}" class="absolute left-2 top-2">
                                @csrf @method('PUT')
                                <button type="submit" title="{{ $photo->is_featured ? 'Retirer de l\'accueil' : 'Mettre en avant sur l\'accueil' }}"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-white/90 shadow hover:bg-white">
                                    <i class="{{ $photo->is_featured ? 'ph-fill text-gold-500' : 'ph text-gray-500' }} ph-star"></i>
                                </button>
                            </form>
                        </div>
                        <div class="p-3">
                            <div x-show="!edit" class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-gray-800">{{ $photo->title_fr ?: 'Sans titre' }}</p>
                                    <p class="truncate text-xs text-gray-500">{{ $photo->caption_fr }}</p>
                                </div>
                                <div class="flex shrink-0">
                                    <x-button type="button" variant="ghost" size="xs" @click="edit = true"><i class="ph ph-pencil-simple"></i></x-button>
                                    <form method="POST" action="{{ route('photos.destroy', $photo) }}" onsubmit="return confirm('Supprimer cette photo ?')">
                                        @csrf @method('DELETE')
                                        <x-button type="submit" variant="ghost" size="xs" class="text-red-600"><i class="ph ph-trash"></i></x-button>
                                    </form>
                                </div>
                            </div>
                            <form x-show="edit" x-cloak method="POST" action="{{ route('photos.update', $photo) }}" class="space-y-2">
                                @csrf @method('PUT')
                                <div class="flex gap-1 text-xs">
                                    <button type="button" @click="lang = 'fr'" :class="lang === 'fr' ? 'bg-brand-500 text-white' : 'bg-gray-100'" class="rounded px-2 py-0.5">FR</button>
                                    <button type="button" @click="lang = 'en'" :class="lang === 'en' ? 'bg-brand-500 text-white' : 'bg-gray-100'" class="rounded px-2 py-0.5">EN</button>
                                </div>
                                @foreach (\App\Support\Locales::SUPPORTED as $locale)
                                    <div x-show="lang === '{{ $locale }}'" class="space-y-2">
                                        <input name="title_{{ $locale }}" value="{{ $photo->{'title_' . $locale} }}" placeholder="Titre" maxlength="150" class="block h-8 w-full rounded border-gray-300 text-sm">
                                        <textarea name="caption_{{ $locale }}" rows="2" placeholder="Légende" maxlength="500" class="block w-full rounded border-gray-300 text-sm">{{ $photo->{'caption_' . $locale} }}</textarea>
                                    </div>
                                @endforeach
                                <div class="flex gap-1">
                                    <x-button type="submit" size="xs">Enregistrer</x-button>
                                    <x-button type="button" variant="ghost" size="xs" @click="edit = false">Annuler</x-button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('albums.update', $album) }}" x-data="{ lang: 'fr' }">
        @csrf @method('PUT')
        <x-admin.panel title="Album">
            <x-admin.lang-tabs :sticky="false" />
            <x-admin.translated name="name" label="Nom" :model="$album" required maxlength="150" />
            <x-admin.translated name="description" label="Description" type="textarea" rows="3" :model="$album" maxlength="1000" />
            <x-button type="submit" class="w-full"><i class="ph ph-floppy-disk mr-2"></i>Enregistrer</x-button>
        </x-admin.panel>
    </form>
</div>
@endsection
