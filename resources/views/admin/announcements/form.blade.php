@extends('layouts.app')
@section('title', ($announcement->exists ? 'Modifier le communiqué' : 'Nouveau communiqué') . ' - Admin Ambassade')

@section('content')
<x-admin.header :title="$announcement->exists ? 'Modifier le communiqué' : 'Nouveau communiqué'"
    :items="['Tableau de bord' => route('dashboard'), 'Communiqués' => route('announcements.index'), ($announcement->exists ? 'Modifier' : 'Nouveau') => null]" />

<form method="POST" enctype="multipart/form-data" x-data="{ lang: 'fr' }"
    action="{{ $announcement->exists ? route('announcements.update', $announcement) : route('announcements.store') }}" class="grid gap-6 xl:grid-cols-[1fr_22rem]">
    @csrf
    @if ($announcement->exists) @method('PUT') @endif

    <div class="space-y-6">
        <x-admin.lang-tabs />
        <x-admin.panel>
            <x-admin.translated name="title" label="Titre" :model="$announcement" required maxlength="255" />
            <x-admin.translated name="body" label="Texte" type="rich" :model="$announcement" />
        </x-admin.panel>
    </div>

    <div class="space-y-6">
        <x-admin.panel title="Publication">
            <x-admin.choice name="status" label="Statut" :options="\App\Models\Announcement::STATUSES" :value="$announcement->status" required />
            <div class="grid grid-cols-2 gap-3">
                <x-admin.field name="published_on" label="Du" type="date" :value="$announcement->published_on?->format('Y-m-d')" required />
                <x-admin.field name="expires_on" label="Au" type="date" :value="$announcement->expires_on?->format('Y-m-d')" />
            </div>
            <p class="-mt-2 text-xs text-gray-500">Sans date de fin, le communiqué reste en ligne.</p>
            <x-admin.choice name="priority" label="Priorité" :options="\App\Models\Announcement::PRIORITIES" :value="$announcement->priority" required
                help="« Urgente » s'affiche en rouge en haut de l'accueil." />
            <x-admin.field name="category" label="Catégorie" :value="$announcement->category" placeholder="Consulaire, Visa, Communauté…" />
            <x-admin.toggle name="is_pinned" label="Épingler sur l'accueil" :checked="$announcement->is_pinned" />
        </x-admin.panel>

        <x-admin.panel title="Médias">
            <x-admin.image-field label="Image" :path="$announcement->image_path" />
            <x-admin.file-field label="Document PDF joint" :path="$announcement->attachment_path" help="PDF, 20 Mo maximum." />
        </x-admin.panel>

        <div class="flex gap-2">
            <x-button type="submit" class="flex-1"><i class="ph ph-floppy-disk mr-2"></i>Enregistrer</x-button>
            <x-button-link href="{{ route('announcements.index') }}" variant="ghost">Annuler</x-button-link>
        </div>
    </div>
</form>
@endsection
