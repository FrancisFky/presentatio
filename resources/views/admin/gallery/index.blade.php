@extends('layouts.app')
@section('title', 'Galerie - Admin Ambassade')

@section('content')
<x-admin.header title="Galerie" subtitle="Les photos marquées d'une étoile apparaissent sur l'accueil." :items="['Tableau de bord' => route('dashboard'), 'Galerie' => null]" />

<div class="grid gap-6 xl:grid-cols-[1fr_22rem]">
    <div>
        @if ($albums->isEmpty())
            <x-admin.empty icon="ph-images" title="Aucun album" text="Créez un premier album pour y déposer des photos." />
        @else
            <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-3">
                @foreach ($albums as $album)
                    <a href="{{ route('albums.show', $album) }}" class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:border-gold-300 hover:shadow">
                        <div class="aspect-[4/3] bg-gray-100">
                            @if ($album->cover)
                                <img src="{{ $album->cover->url() }}" alt="" class="h-full w-full object-cover" loading="lazy">
                            @else
                                <div class="flex h-full items-center justify-center"><i class="ph ph-images text-4xl text-gray-300"></i></div>
                            @endif
                        </div>
                        <div class="p-4">
                            <p class="font-semibold text-gray-900 group-hover:text-brand-600">{{ $album->name_fr }}</p>
                            <p class="text-sm text-gray-500">{{ $album->photos_count }} photo(s)</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('albums.store') }}" x-data="{ lang: 'fr' }">
        @csrf
        <x-admin.panel title="Nouvel album">
            <x-admin.lang-tabs :sticky="false" />
            <x-admin.translated name="name" label="Nom" required maxlength="150" />
            <x-admin.translated name="description" label="Description" type="textarea" rows="2" maxlength="1000" />
            <x-button type="submit" class="w-full"><i class="ph ph-plus mr-2"></i>Créer l'album</x-button>
        </x-admin.panel>
    </form>
</div>
@endsection
