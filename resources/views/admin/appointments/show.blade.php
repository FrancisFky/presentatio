@extends('layouts.app')
@section('title', 'Rendez-vous ' . $appointment->reference . ' - Admin Ambassade')

@section('content')
<x-admin.header :title="$appointment->name" :subtitle="'Demande ' . $appointment->reference . ' reçue le ' . $appointment->created_at->translatedFormat('j F Y à H:i')"
    :items="['Tableau de bord' => route('dashboard'), 'Rendez-vous' => route('appointments.index'), $appointment->reference => null]">
    <x-slot name="action">
        <x-admin.delete-button :action="route('appointments.destroy', $appointment)" size="sm" label="Supprimer" confirm="Supprimer cette demande ?" />
    </x-slot>
</x-admin.header>

<div class="grid gap-6 xl:grid-cols-[1fr_26rem]">
    <x-admin.panel title="La demande">
        <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2 text-sm">
            <div><dt class="text-gray-500">Service</dt><dd class="font-medium text-gray-900">{{ $appointment->serviceName() }}</dd></div>
            <div><dt class="text-gray-500">Date et heure souhaitées</dt><dd class="font-medium text-gray-900">{{ $appointment->preferred_date->translatedFormat('l j F Y') }} à {{ $appointment->preferred_time }}</dd></div>
            <div><dt class="text-gray-500">E-mail</dt><dd><a href="mailto:{{ $appointment->email }}" class="font-medium text-brand-600 hover:underline">{{ $appointment->email }}</a></dd></div>
            <div><dt class="text-gray-500">Téléphone</dt><dd class="font-medium text-gray-900">@if ($appointment->phone)<a href="tel:{{ $appointment->phone }}" class="hover:underline">{{ $appointment->phone }}</a>@else — @endif</dd></div>
            <div><dt class="text-gray-500">Nationalité</dt><dd class="font-medium text-gray-900">{{ $appointment->nationality ?: '—' }}</dd></div>
            <div><dt class="text-gray-500">Langue</dt><dd class="font-medium text-gray-900">{{ \App\Support\Locales::LABELS[$appointment->locale] ?? $appointment->locale }}</dd></div>
            @if ($appointment->notes)
                <div class="sm:col-span-2"><dt class="text-gray-500">Précisions du demandeur</dt><dd class="mt-1 whitespace-pre-line rounded-lg bg-gray-50 p-3 text-gray-800">{{ $appointment->notes }}</dd></div>
            @endif
        </dl>
    </x-admin.panel>

    <form method="POST" action="{{ route('appointments.update', $appointment) }}" x-data="{ notify: false }">
        @csrf @method('PUT')
        <x-admin.panel title="Réponse">
            <x-admin.choice name="status" label="Statut" :options="\App\Models\Appointment::STATUSES" :value="$appointment->status" required />
            <div class="grid grid-cols-2 gap-3">
                <x-admin.field name="preferred_date" label="Date" type="date" :value="$appointment->preferred_date->format('Y-m-d')" required />
                <x-admin.field name="preferred_time" label="Heure" type="time" :value="$appointment->preferred_time" required />
            </div>
            <div class="space-y-2">
                <label for="admin_notes" class="block text-sm font-medium text-gray-900">Notes internes</label>
                <textarea name="admin_notes" id="admin_notes" rows="3" class="block w-full rounded-lg border-0 px-4 py-2.5 text-sm shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-brand-500">{{ old('admin_notes', $appointment->admin_notes) }}</textarea>
                <p class="text-xs text-gray-500">Jamais envoyées au demandeur.</p>
            </div>
            <label class="flex items-start gap-3 rounded-lg bg-brand-50 p-3">
                <input type="checkbox" name="notify" value="1" x-model="notify" class="mt-0.5 h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                <span class="text-sm"><span class="font-medium text-gray-900">Prévenir le demandeur par e-mail</span><br><span class="text-gray-600">Statut, date et heure, dans sa langue ({{ strtoupper($appointment->locale) }}).</span></span>
            </label>
            <div x-show="notify" x-cloak class="space-y-2">
                <label for="message" class="block text-sm font-medium text-gray-900">Message au demandeur</label>
                <textarea name="message" id="message" rows="3" placeholder="Pièces à apporter, porte d'entrée…" class="block w-full rounded-lg border-0 px-4 py-2.5 text-sm shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-brand-500">{{ old('message') }}</textarea>
            </div>
            <x-button type="submit" class="w-full"><i class="ph ph-floppy-disk mr-2"></i>Enregistrer</x-button>
        </x-admin.panel>
    </form>
</div>
@endsection
