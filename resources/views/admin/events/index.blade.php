@extends('layouts.app')
@section('title', 'Événements - Admin Ambassade')

@section('content')
<x-admin.header title="Événements" :items="['Tableau de bord' => route('dashboard'), 'Événements' => null]">
    <x-slot name="action"><x-button-link href="{{ route('events.create') }}"><i class="ph ph-plus mr-2"></i>Nouvel événement</x-button-link></x-slot>
</x-admin.header>

<x-admin.filters placeholder="Titre…">
    <x-admin.filter-select name="status" :options="\App\Models\Event::STATUSES" placeholder="Tous les statuts" />
</x-admin.filters>

@if ($events->isEmpty())
    <x-admin.empty icon="ph-calendar-star" title="Aucun événement" text="Réceptions, fête nationale, rencontres avec la diaspora…" />
@else
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="admin-table min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                <tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Événement</th><th class="px-4 py-3">Statut</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($events as $event)
                    @php $past = $event->starts_on->isBefore(today()); @endphp
                    <tr class="hover:bg-gray-50/60 {{ $past ? 'text-gray-400' : '' }}">
                        <td class="px-4 py-3">
                            <p class="font-medium {{ $past ? '' : 'text-gray-900' }}">{{ $event->starts_on->translatedFormat('d M Y') }}</p>
                            <p class="text-xs">{{ $event->time() ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('events.edit', $event) }}" class="font-medium {{ $past ? '' : 'text-gray-900' }} hover:text-brand-600">{{ $event->title_fr ?: $event->title_en }}</a>
                            <div class="mt-1 flex items-center gap-2"><x-admin.lang-status :model="$event" /><span class="text-xs">{{ $event->venue_fr }}</span></div>
                        </td>
                        <td class="px-4 py-3"><x-status-badge :status="$event->status" :labels="\App\Models\Event::STATUSES" /></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <x-button-link href="{{ route('events.edit', $event) }}" variant="ghost" size="xs" title="Modifier"><i class="ph ph-pencil-simple"></i></x-button-link>
                                <x-admin.delete-button :action="route('events.destroy', $event)" confirm="Supprimer cet événement ?" />
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $events->links() }}</div>
@endif
@endsection
