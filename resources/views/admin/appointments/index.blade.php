@extends('layouts.app')
@section('title', 'Rendez-vous - Admin Ambassade')

@section('content')
<x-admin.header title="Rendez-vous" subtitle="Demandes envoyées depuis le formulaire du site." :items="['Tableau de bord' => route('dashboard'), 'Rendez-vous' => null]" />

<div class="mb-4 flex flex-wrap gap-2">
    <a href="{{ route('appointments.index') }}" class="rounded-lg px-3 py-1.5 text-sm font-medium {{ !request('status') ? 'bg-brand-500 text-white' : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50' }}">Toutes <span class="opacity-70">{{ $counts->sum() }}</span></a>
    @foreach (\App\Models\Appointment::STATUSES as $status => $label)
        <a href="{{ route('appointments.index', ['status' => $status] + request()->except('status', 'page')) }}"
            class="rounded-lg px-3 py-1.5 text-sm font-medium {{ request('status') === $status ? 'bg-brand-500 text-white' : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50' }}">{{ $label }} <span class="opacity-70">{{ $counts[$status] ?? 0 }}</span></a>
    @endforeach
</div>

<x-admin.filters placeholder="Nom, e-mail ou référence…">
    @if (request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    <x-admin.filter-select name="service" :options="$services" placeholder="Tous les services" />
    <x-admin.filter-select name="when" :options="['upcoming' => 'À venir, par date']" placeholder="Plus récentes d'abord" />
</x-admin.filters>

@if ($appointments->isEmpty())
    <x-admin.empty icon="ph-calendar-check" title="Aucune demande" />
@else
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="admin-table min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                <tr><th class="px-4 py-3">Référence</th><th class="px-4 py-3">Demandeur</th><th class="px-4 py-3">Service</th><th class="px-4 py-3">Date souhaitée</th><th class="px-4 py-3">Statut</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($appointments as $appointment)
                    <tr class="hover:bg-gray-50/60">
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $appointment->reference }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('appointments.show', $appointment) }}" class="font-medium text-gray-900 hover:text-brand-600">{{ $appointment->name }}</a>
                            <p class="text-xs text-gray-500">{{ $appointment->email }} · reçue {{ $appointment->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ Str::limit($appointment->serviceName(), 30) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $appointment->preferred_date->translatedFormat('D d M Y') }} · {{ $appointment->preferred_time }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$appointment->status" :labels="\App\Models\Appointment::STATUSES" /></td>
                        <td class="px-4 py-3"><x-button-link href="{{ route('appointments.show', $appointment) }}" variant="ghost" size="xs">Traiter <i class="ph ph-arrow-right ml-1"></i></x-button-link></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $appointments->links() }}</div>
@endif
@endsection
