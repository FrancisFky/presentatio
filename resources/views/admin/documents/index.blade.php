@extends('layouts.app')
@section('title', 'Documents - Admin Ambassade')

@section('content')
<x-admin.header title="Documents" subtitle="Formulaires et publications téléchargeables depuis le site."
    :items="['Tableau de bord' => route('dashboard'), 'Documents' => null]">
    <x-slot name="action"><x-button-link href="{{ route('documents.create') }}"><i class="ph ph-upload-simple mr-2"></i>Ajouter un document</x-button-link></x-slot>
</x-admin.header>

<x-admin.filters placeholder="Titre…">
    <x-admin.filter-select name="category" :options="array_combine($categories, $categories)" placeholder="Toutes les catégories" />
    <x-admin.filter-select name="status" :options="\App\Models\Document::STATUSES" placeholder="Tous les statuts" />
</x-admin.filters>

@if ($documents->isEmpty())
    <x-admin.empty icon="ph-files" title="Aucun document" text="Formulaire de demande de visa, de passeport, guides pratiques…" />
@else
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="admin-table min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                <tr><th class="px-4 py-3"></th><th class="px-4 py-3">Document</th><th class="px-4 py-3">Catégorie</th><th class="px-4 py-3">Téléchargements</th><th class="px-4 py-3">Statut</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($documents as $document)
                    <tr class="hover:bg-gray-50/60">
                        <td class="px-4 py-3"><i class="ph {{ $document->icon() }} text-2xl text-brand-500"></i></td>
                        <td class="px-4 py-3">
                            <a href="{{ route('documents.edit', $document) }}" class="font-medium text-gray-900 hover:text-brand-600">{{ $document->title_fr }}</a>
                            <p class="text-xs text-gray-500">{{ strtoupper($document->extension()) }}{{ $document->humanSize() ? ' · ' . $document->humanSize() : '' }} · <x-admin.lang-status :model="$document" /></p>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $document->category ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $document->download_count }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$document->status" :labels="\App\Models\Document::STATUSES" /></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <x-button-link href="{{ Storage::disk('public')->url($document->file_path) }}" target="_blank" variant="ghost" size="xs" title="Ouvrir"><i class="ph ph-eye"></i></x-button-link>
                                <x-button-link href="{{ route('documents.edit', $document) }}" variant="ghost" size="xs" title="Modifier"><i class="ph ph-pencil-simple"></i></x-button-link>
                                <x-admin.delete-button :action="route('documents.destroy', $document)" confirm="Supprimer ce document ?" />
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $documents->links() }}</div>
@endif
@endsection
