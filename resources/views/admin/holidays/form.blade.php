@extends('layouts.app')
@section('title', ($holiday->exists ? 'Modifier le jour férié' : 'Nouveau jour férié') . ' - Admin Ambassade')

@section('content')
<x-admin.header :title="$holiday->exists ? 'Modifier le jour férié' : 'Nouveau jour férié'"
    :items="['Tableau de bord' => route('dashboard'), 'Jours fériés' => route('holidays.index'), ($holiday->exists ? 'Modifier' : 'Nouveau') => null]" />

<form method="POST" x-data="{ lang: 'fr' }" class="max-w-3xl space-y-6"
    action="{{ $holiday->exists ? route('holidays.update', $holiday) : route('holidays.store') }}">
    @csrf
    @if ($holiday->exists) @method('PUT') @endif

    <x-admin.lang-tabs :sticky="false" />
    <x-admin.panel>
        <x-admin.translated name="name" label="Nom" :model="$holiday" required maxlength="255" />
        <x-admin.translated name="description" label="Précision" type="textarea" rows="2" :model="$holiday" maxlength="1000" help="Par exemple : « Fête de l'Indépendance du Congo »." />
        <div class="grid gap-4 sm:grid-cols-2">
            <x-admin.field name="date" label="Date" type="date" :value="$holiday->date?->format('Y-m-d')" required />
            <x-admin.choice name="status" label="Statut" :options="\App\Models\Holiday::STATUSES" :value="$holiday->status" required />
        </div>
    </x-admin.panel>

    <div class="flex gap-2">
        <x-button type="submit"><i class="ph ph-floppy-disk mr-2"></i>Enregistrer</x-button>
        <x-button-link href="{{ route('holidays.index') }}" variant="ghost">Annuler</x-button-link>
    </div>
</form>
@endsection
