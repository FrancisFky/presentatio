@extends('layouts.app')
@section('title', 'Services consulaires - Admin Ambassade')

@section('content')
<x-admin.header title="Services consulaires" subtitle="Proposés sur le site et dans le formulaire de rendez-vous, dans l'ordre ci-dessous."
    :items="['Tableau de bord' => route('dashboard'), 'Services' => null]">
    <x-slot name="action"><x-button-link href="{{ route('services.create') }}"><i class="ph ph-plus mr-2"></i>Nouveau service</x-button-link></x-slot>
</x-admin.header>

@if ($services->isEmpty())
    <x-admin.empty icon="ph-identification-card" title="Aucun service" text="Passeport, visa, légalisation, état civil…" />
@else
    <div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-3">
        @foreach ($services as $service)
            <div class="flex flex-col rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-500">
                        <i class="ph ph-{{ $service->icon ?: 'file-text' }} text-2xl"></i>
                    </span>
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('services.edit', $service) }}" class="font-semibold text-gray-900 hover:text-brand-600">{{ $service->title_fr ?: $service->title_en }}</a>
                        <p class="text-sm text-gray-500">{{ $service->title_en }}</p>
                    </div>
                    <span class="text-xs text-gray-400">#{{ $service->position }}</span>
                </div>
                <p class="mt-3 line-clamp-2 flex-1 text-sm text-gray-600">{{ Str::limit(strip_tags($service->description_fr), 140) }}</p>
                <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-3">
                    <div class="flex items-center gap-2">
                        <x-status-badge :status="$service->status" :labels="\App\Models\Service::STATUSES" />
                        <x-admin.lang-status :model="$service" />
                        <span class="text-xs text-gray-500">{{ $service->appointments_count }} RDV</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <x-button-link href="{{ route('services.edit', $service) }}" variant="ghost" size="xs"><i class="ph ph-pencil-simple"></i></x-button-link>
                        <x-admin.delete-button :action="route('services.destroy', $service)" confirm="Supprimer ce service ? Les rendez-vous déjà demandés sont conservés." />
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
