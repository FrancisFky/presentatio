@extends('layouts.app')
@section('title', ($event->exists ? 'Modifier l\'événement' : 'Nouvel événement') . ' - Admin Ambassade')

@section('content')
<x-admin.header :title="$event->exists ? 'Modifier l\'événement' : 'Nouvel événement'"
    :items="['Tableau de bord' => route('dashboard'), 'Événements' => route('events.index'), ($event->exists ? 'Modifier' : 'Nouveau') => null]" />

<form method="POST" enctype="multipart/form-data" x-data="{ lang: 'fr' }"
    action="{{ $event->exists ? route('events.update', $event) : route('events.store') }}" class="grid gap-6 xl:grid-cols-[1fr_22rem]">
    @csrf
    @if ($event->exists) @method('PUT') @endif

    <div class="space-y-6">
        <x-admin.lang-tabs />
        <x-admin.panel>
            <x-admin.translated name="title" label="Titre" :model="$event" required maxlength="255" />
            <x-admin.translated name="venue" label="Lieu" :model="$event" maxlength="255" />
            <x-admin.translated name="description" label="Description" type="rich" :model="$event" />
        </x-admin.panel>
    </div>

    <div class="space-y-6">
        <x-admin.panel title="Quand">
            <x-admin.choice name="status" label="Statut" :options="\App\Models\Event::STATUSES" :value="$event->status" required />
            <div class="grid grid-cols-2 gap-3">
                <x-admin.field name="starts_on" label="Date" type="date" :value="$event->starts_on?->format('Y-m-d')" required />
                <x-admin.field name="starts_at" label="Heure" type="time" :value="$event->time()" />
            </div>
        </x-admin.panel>
        <x-admin.panel title="Détails">
            <x-admin.field name="organizer" label="Organisateur" :value="$event->organizer" />
            <x-admin.field name="speaker" label="Intervenant(s)" :value="$event->speaker" />
            <x-admin.field name="registration_url" label="Lien d'inscription" type="url" :value="$event->registration_url" placeholder="https://" />
            <x-admin.image-field label="Visuel" :path="$event->image_path" />
        </x-admin.panel>

        <div class="flex gap-2">
            <x-button type="submit" class="flex-1"><i class="ph ph-floppy-disk mr-2"></i>Enregistrer</x-button>
            <x-button-link href="{{ route('events.index') }}" variant="ghost">Annuler</x-button-link>
        </div>
    </div>
</form>
@endsection
