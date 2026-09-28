@extends('layouts.app')
@section('title', 'Modifier « ' . $page->title_fr . ' » - Admin Ambassade')

@section('content')
<x-admin.header :title="$page->title_fr" :items="['Tableau de bord' => route('dashboard'), 'Pages' => route('pages.index'), 'Modifier' => null]">
    <x-slot name="action">
        @if ($page->isPublished())
            <x-button-link href="{{ route('site.page', ['locale' => 'fr', 'page' => $page]) }}" target="_blank" variant="outline"><i class="ph ph-arrow-square-out mr-2"></i>Voir la page</x-button-link>
        @endif
    </x-slot>
</x-admin.header>

<form method="POST" enctype="multipart/form-data" x-data="{ lang: 'fr' }" action="{{ route('pages.update', $page) }}" class="grid gap-6 xl:grid-cols-[1fr_22rem]">
    @csrf
    @method('PUT')

    <div class="space-y-6">
        <x-admin.lang-tabs />
        <x-admin.panel>
            <x-admin.translated name="title" label="Titre" :model="$page" required maxlength="255" />
            <x-admin.translated name="body" label="Contenu" type="rich" :model="$page" />
        </x-admin.panel>
    </div>

    <div class="space-y-6">
        <x-admin.panel title="Publication">
            <x-admin.choice name="status" label="Statut" :options="\App\Models\Page::STATUSES" :value="$page->status" required />
            <x-admin.image-field label="Photo d'en-tête" :path="$page->image_path" help="Paysage, au moins 1920 px de large." />
        </x-admin.panel>
        <div class="flex gap-2">
            <x-button type="submit" class="flex-1"><i class="ph ph-floppy-disk mr-2"></i>Enregistrer</x-button>
            <x-button-link href="{{ route('pages.index') }}" variant="ghost">Annuler</x-button-link>
        </div>
    </div>
</form>
@endsection
