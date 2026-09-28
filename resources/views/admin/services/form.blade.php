@extends('layouts.app')
@section('title', ($service->exists ? 'Modifier le service' : 'Nouveau service') . ' - Admin Ambassade')

@section('content')
<x-admin.header :title="$service->exists ? 'Modifier le service' : 'Nouveau service'"
    :items="['Tableau de bord' => route('dashboard'), 'Services' => route('services.index'), ($service->exists ? 'Modifier' : 'Nouveau') => null]" />

<form method="POST" x-data="{ lang: 'fr' }"
    action="{{ $service->exists ? route('services.update', $service) : route('services.store') }}" class="grid gap-6 xl:grid-cols-[1fr_22rem]">
    @csrf
    @if ($service->exists) @method('PUT') @endif

    <div class="space-y-6">
        <x-admin.lang-tabs />
        <x-admin.panel>
            <x-admin.translated name="title" label="Nom du service" :model="$service" required maxlength="255" />
            <x-admin.translated name="description" label="Présentation" type="rich" :model="$service" />
        </x-admin.panel>
        <x-admin.panel title="Constituer son dossier">
            <x-admin.translated name="requirements" label="Conditions" type="rich" :model="$service" />
            <x-admin.translated name="documents" label="Pièces à fournir" type="rich" :model="$service" help="Une liste à puces se lit mieux." />
            <x-admin.translated name="fees" label="Frais" type="rich" :model="$service" />
        </x-admin.panel>
        <x-admin.panel title="Délais et horaires">
            <x-admin.translated name="processing_time" label="Délai de traitement" :model="$service" maxlength="255" />
            <x-admin.translated name="office_hours" label="Heures de dépôt" :model="$service" maxlength="255" />
        </x-admin.panel>
    </div>

    <div class="space-y-6">
        <x-admin.panel title="Affichage">
            <x-admin.choice name="status" label="Statut" :options="\App\Models\Service::STATUSES" :value="$service->status" required />
            <x-admin.field name="position" label="Ordre d'affichage" type="number" min="0" :value="$service->position" required />
            <div class="space-y-2" x-data="{ icon: @js(old('icon', $service->icon)) }">
                <span class="block text-sm font-medium text-gray-900">Icône</span>
                <input type="hidden" name="icon" :value="icon">
                <div class="grid grid-cols-4 gap-2">
                    @foreach (\App\Models\Service::ICONS as $name => $label)
                        <button type="button" @click="icon = '{{ $name }}'" title="{{ $label }}"
                            :class="icon === '{{ $name }}' ? 'bg-brand-500 text-white ring-brand-500' : 'bg-white text-gray-600 ring-gray-200 hover:bg-gray-50'"
                            class="flex h-12 items-center justify-center rounded-lg ring-1 transition">
                            <i class="ph ph-{{ $name }} text-2xl"></i>
                        </button>
                    @endforeach
                </div>
            </div>
        </x-admin.panel>

        <div class="flex gap-2">
            <x-button type="submit" class="flex-1"><i class="ph ph-floppy-disk mr-2"></i>Enregistrer</x-button>
            <x-button-link href="{{ route('services.index') }}" variant="ghost">Annuler</x-button-link>
        </div>
    </div>
</form>
@endsection
