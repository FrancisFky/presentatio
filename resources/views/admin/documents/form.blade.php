@extends('layouts.app')
@section('title', ($document->exists ? 'Modifier le document' : 'Nouveau document') . ' - Admin Ambassade')

@section('content')
<x-admin.header :title="$document->exists ? 'Modifier le document' : 'Nouveau document'"
    :items="['Tableau de bord' => route('dashboard'), 'Documents' => route('documents.index'), ($document->exists ? 'Modifier' : 'Nouveau') => null]" />

<form method="POST" enctype="multipart/form-data" x-data="{ lang: 'fr' }" class="max-w-3xl space-y-6"
    action="{{ $document->exists ? route('documents.update', $document) : route('documents.store') }}">
    @csrf
    @if ($document->exists) @method('PUT') @endif

    <x-admin.lang-tabs :sticky="false" />
    <x-admin.panel>
        <x-admin.translated name="title" label="Titre" :model="$document" required maxlength="255" />
        <div class="space-y-2">
            <x-admin.field name="category" label="Catégorie" :value="$document->category" list="document-categories" placeholder="Visas, Passeports, État civil…" />
            <datalist id="document-categories">@foreach ($categories as $category)<option value="{{ $category }}">@endforeach</datalist>
        </div>
        <x-admin.file-field name="file" label="Fichier" :path="$document->file_path" :removable="false" :required="!$document->exists"
            accept="{{ collect(\App\Models\Document::EXTENSIONS)->map(fn ($e) => '.' . $e)->join(',') }}"
            help="PDF, Word, Excel, ZIP ou image, 20 Mo maximum." />
        <x-admin.choice name="status" label="Statut" :options="\App\Models\Document::STATUSES" :value="$document->status" required />
    </x-admin.panel>

    <div class="flex gap-2">
        <x-button type="submit"><i class="ph ph-floppy-disk mr-2"></i>Enregistrer</x-button>
        <x-button-link href="{{ route('documents.index') }}" variant="ghost">Annuler</x-button-link>
    </div>
</form>
@endsection
