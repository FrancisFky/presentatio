@extends('layouts.app')
@section('title', 'Tableau de bord - Admin Ambassade')

@section('content')
<x-admin.header :title="'Bonjour, ' . Str::before(auth('admin')->user()->name, ' ')" :subtitle="now()->translatedFormat('l j F Y')" />

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($stats as $stat)
        <a href="{{ $stat['route'] }}" class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-gold-300 hover:shadow">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">{{ $stat['label'] }}</p>
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-500 group-hover:bg-gold-100 group-hover:text-gold-700">
                    <i class="ph ph-{{ $stat['icon'] }} text-xl"></i>
                </span>
            </div>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $stat['value'] }}</p>
        </a>
    @endforeach
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-3">
    <x-admin.panel title="Rendez-vous à traiter" class="xl:col-span-2">
        @forelse ($appointments as $appointment)
            <a href="{{ route('appointments.show', $appointment) }}" class="-mx-2 flex items-center gap-4 rounded-lg px-2 py-2 hover:bg-gray-50">
                <div class="w-14 shrink-0 rounded-lg bg-brand-50 py-1 text-center text-brand-600">
                    <p class="text-lg font-semibold leading-none">{{ $appointment->preferred_date->format('d') }}</p>
                    <p class="text-[11px] uppercase">{{ $appointment->preferred_date->translatedFormat('M') }}</p>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate font-medium text-gray-900">{{ $appointment->name }}</p>
                    <p class="truncate text-sm text-gray-500">{{ $appointment->serviceName() }} · {{ $appointment->preferred_time }}</p>
                </div>
                <span class="text-xs text-gray-400">{{ $appointment->created_at->diffForHumans() }}</span>
            </a>
        @empty
            <p class="text-sm text-gray-500"><i class="ph ph-check-circle mr-1 text-green-600"></i>Aucune demande en attente.</p>
        @endforelse
        @if ($appointments->isNotEmpty())
            <a href="{{ route('appointments.index', ['status' => 'pending']) }}" class="inline-flex items-center gap-1 text-sm font-medium text-brand-600 hover:underline">Toutes les demandes <i class="ph ph-arrow-right"></i></a>
        @endif
    </x-admin.panel>

    <x-admin.panel title="Messages non lus">
        @forelse ($messages as $message)
            <a href="{{ route('messages.show', $message) }}" class="-mx-2 block rounded-lg px-2 py-2 hover:bg-gray-50">
                <p class="truncate text-sm font-medium text-gray-900">{{ $message->subject ?: 'Sans objet' }}</p>
                <p class="truncate text-xs text-gray-500">{{ $message->name }} · {{ $message->created_at->diffForHumans() }}</p>
            </a>
        @empty
            <p class="text-sm text-gray-500"><i class="ph ph-check-circle mr-1 text-green-600"></i>Boîte de réception à jour.</p>
        @endforelse
    </x-admin.panel>

    <x-admin.panel title="Prochains événements">
        @forelse ($upcomingEvents as $event)
            <a href="{{ route('events.edit', $event) }}" class="-mx-2 block rounded-lg px-2 py-2 hover:bg-gray-50">
                <p class="truncate text-sm font-medium text-gray-900">{{ $event->title_fr ?: $event->title_en }}</p>
                <p class="text-xs text-gray-500">{{ $event->starts_on->translatedFormat('l j F') }}{{ $event->time() ? ' · ' . $event->time() : '' }}</p>
            </a>
        @empty
            <p class="text-sm text-gray-500">Aucun événement publié à venir.</p>
        @endforelse
    </x-admin.panel>

    <x-admin.panel title="Brouillons récents">
        @forelse ($drafts as $draft)
            <a href="{{ route('news.edit', $draft) }}" class="-mx-2 block rounded-lg px-2 py-2 hover:bg-gray-50">
                <p class="truncate text-sm font-medium text-gray-900">{{ $draft->title_fr ?: $draft->title_en }}</p>
                <p class="text-xs text-gray-500">Modifié {{ $draft->updated_at->diffForHumans() }}</p>
            </a>
        @empty
            <p class="text-sm text-gray-500">Aucun brouillon.</p>
        @endforelse
    </x-admin.panel>

    <x-admin.panel title="En bref">
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Communiqués en ligne</dt><dd class="font-semibold">{{ $activeAnnouncements }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Téléchargements de documents</dt><dd class="font-semibold">{{ number_format($downloads, 0, ',', ' ') }}</dd></div>
        </dl>
        <div class="grid grid-cols-2 gap-2 pt-2">
            <x-button-link href="{{ route('news.create') }}" variant="outline" size="sm"><i class="ph ph-plus mr-1"></i>Article</x-button-link>
            <x-button-link href="{{ route('announcements.create') }}" variant="outline" size="sm"><i class="ph ph-plus mr-1"></i>Communiqué</x-button-link>
        </div>
    </x-admin.panel>
</div>
@endsection
