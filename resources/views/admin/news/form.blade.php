@extends('layouts.app')
@section('title', ($news->exists ? 'Modifier l\'article' : 'Nouvel article') . ' - Admin Ambassade')

@section('content')
<x-admin.header :title="$news->exists ? 'Modifier l\'article' : 'Nouvel article'"
    :items="['Tableau de bord' => route('dashboard'), 'Actualités' => route('news.index'), ($news->exists ? 'Modifier' : 'Nouveau') => null]" />

<form method="POST" enctype="multipart/form-data" x-data="{ lang: 'fr' }"
    action="{{ $news->exists ? route('news.update', $news) : route('news.store') }}" class="grid gap-6 xl:grid-cols-[1fr_22rem]">
    @csrf
    @if ($news->exists) @method('PUT') @endif

    <div class="space-y-6">
        <x-admin.lang-tabs />
        <x-admin.panel>
            <x-admin.translated name="title" label="Titre" :model="$news" required maxlength="255" />
            <x-admin.translated name="excerpt" label="Chapeau" type="textarea" rows="3" :model="$news" maxlength="500"
                help="Deux ou trois phrases, affichées sous le titre et dans les listes." />
            <x-admin.translated name="body" label="Texte de l'article" type="rich" :model="$news" />
        </x-admin.panel>
    </div>

    <div class="space-y-6">
        <x-admin.panel title="Publication">
            <x-admin.choice name="status" label="Statut" :options="\App\Models\News::STATUSES" :value="$news->status" required />
            <x-admin.field name="published_on" label="Date de publication" type="date" :value="$news->published_on?->format('Y-m-d')" required
                help="Une date future programme l'article." />
            <x-admin.choice name="news_category_id" label="Rubrique" :options="$categories" :value="$news->news_category_id" placeholder="— Aucune —" />
            <x-admin.field name="author" label="Auteur" :value="$news->author" />
            @if ($news->exists)
                <x-admin.field name="slug" label="Adresse (URL)" :value="$news->slug" help="Lettres, chiffres et tirets. Changer l'adresse casse les liens déjà partagés." />
            @endif
        </x-admin.panel>

        <x-admin.panel title="Médias">
            <x-admin.image-field label="Photo principale" :path="$news->image_path" />
            <x-admin.file-field label="Document PDF joint" :path="$news->attachment_path" help="PDF, 20 Mo maximum." />
        </x-admin.panel>

        <div class="flex gap-2">
            <x-button type="submit" class="flex-1"><i class="ph ph-floppy-disk mr-2"></i>Enregistrer</x-button>
            <x-button-link href="{{ route('news.index') }}" variant="ghost">Annuler</x-button-link>
        </div>
    </div>
</form>
@endsection
