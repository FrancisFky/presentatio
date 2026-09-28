@extends('layouts.app')
@section('title', 'Communiqués - Admin Ambassade')

@section('content')
<x-admin.header title="Communiqués" subtitle="Avis officiels et informations pratiques. Une date de fin les retire du site d'eux-mêmes."
    :items="['Tableau de bord' => route('dashboard'), 'Communiqués' => null]">
    <x-slot name="action"><x-button-link href="{{ route('announcements.create') }}"><i class="ph ph-plus mr-2"></i>Nouveau communiqué</x-button-link></x-slot>
</x-admin.header>

<x-admin.filters placeholder="Titre…">
    <x-admin.filter-select name="status" :options="\App\Models\Announcement::STATUSES" placeholder="Tous les statuts" />
</x-admin.filters>

@if ($announcements->isEmpty())
    <x-admin.empty icon="ph-megaphone" title="Aucun communiqué" />
@else
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="admin-table min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                <tr><th class="px-4 py-3">Priorité</th><th class="px-4 py-3">Titre</th><th class="px-4 py-3">En ligne</th><th class="px-4 py-3">Statut</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($announcements as $announcement)
                    <tr class="hover:bg-gray-50/60">
                        <td class="px-4 py-3"><x-status-badge :status="$announcement->priority" :labels="\App\Models\Announcement::PRIORITIES" /></td>
                        <td class="px-4 py-3">
                            <a href="{{ route('announcements.edit', $announcement) }}" class="font-medium text-gray-900 hover:text-brand-600">
                                @if ($announcement->is_pinned)<i class="ph-fill ph-push-pin mr-1 text-gold-500" title="Épinglé sur l'accueil"></i>@endif{{ $announcement->title_fr ?: $announcement->title_en }}
                            </a>
                            <div class="mt-1 flex items-center gap-2"><x-admin.lang-status :model="$announcement" />@if ($announcement->category)<span class="text-xs text-gray-500">{{ $announcement->category }}</span>@endif</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $announcement->published_on->format('d/m/Y') }} → {{ $announcement->expires_on?->format('d/m/Y') ?? '…' }}
                        </td>
                        <td class="px-4 py-3">
                            <x-status-badge :status="$announcement->isExpired() ? 'expired' : $announcement->status" :labels="\App\Models\Announcement::STATUSES + ['expired' => 'Expiré']" />
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <x-button-link href="{{ route('announcements.edit', $announcement) }}" variant="ghost" size="xs" title="Modifier"><i class="ph ph-pencil-simple"></i></x-button-link>
                                <x-admin.delete-button :action="route('announcements.destroy', $announcement)" confirm="Supprimer ce communiqué ?" />
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $announcements->links() }}</div>
@endif
@endsection
