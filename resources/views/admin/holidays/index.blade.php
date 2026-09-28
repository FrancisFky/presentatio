@extends('layouts.app')
@section('title', 'Jours fériés - Admin Ambassade')

@section('content')
<x-admin.header title="Jours fériés" subtitle="Jours de fermeture de l'ambassade : affichés sur le site et bloqués dans le formulaire de rendez-vous."
    :items="['Tableau de bord' => route('dashboard'), 'Jours fériés' => null]">
    <x-slot name="action"><x-button-link href="{{ route('holidays.create') }}"><i class="ph ph-plus mr-2"></i>Ajouter</x-button-link></x-slot>
</x-admin.header>

<div class="mb-4 flex flex-wrap gap-2">
    @foreach ($years as $y)
        <a href="{{ route('holidays.index', ['year' => $y]) }}"
            class="rounded-lg px-3 py-1.5 text-sm font-medium {{ $y === $year ? 'bg-brand-500 text-white' : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50' }}">{{ $y }}</a>
    @endforeach
</div>

@if ($holidays->isEmpty())
    <x-admin.empty icon="ph-calendar-x" title="Aucun jour férié en {{ $year }}" />
@else
    <div class="divide-y divide-gray-100 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        @foreach ($holidays as $holiday)
            <div class="flex items-center gap-4 px-4 py-3 {{ $holiday->date->isPast() && !$holiday->date->isToday() ? 'opacity-60' : '' }}">
                <div class="w-14 shrink-0 rounded-lg bg-gold-50 py-1 text-center text-gold-700">
                    <p class="text-lg font-semibold leading-none">{{ $holiday->date->format('d') }}</p>
                    <p class="text-[11px] uppercase">{{ $holiday->date->translatedFormat('M') }}</p>
                </div>
                <div class="min-w-0 flex-1">
                    <a href="{{ route('holidays.edit', $holiday) }}" class="font-medium text-gray-900 hover:text-brand-600">{{ $holiday->name_fr }}</a>
                    <p class="text-sm text-gray-500">{{ $holiday->date->translatedFormat('l') }}@if ($holiday->name_en) · {{ $holiday->name_en }}@endif</p>
                </div>
                <x-status-badge :status="$holiday->status" :labels="\App\Models\Holiday::STATUSES" />
                <div class="flex items-center gap-1">
                    <x-button-link href="{{ route('holidays.edit', $holiday) }}" variant="ghost" size="xs"><i class="ph ph-pencil-simple"></i></x-button-link>
                    <x-admin.delete-button :action="route('holidays.destroy', $holiday)" confirm="Supprimer ce jour férié ?" />
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
