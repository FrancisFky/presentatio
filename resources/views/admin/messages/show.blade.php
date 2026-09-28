@extends('layouts.app')
@section('title', 'Message de ' . $message->name . ' - Admin Ambassade')

@section('content')
<x-admin.header :title="$message->subject ?: 'Sans objet'" :items="['Tableau de bord' => route('dashboard'), 'Messages' => route('messages.index'), $message->name => null]">
    <x-slot name="action">
        <x-button-link href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: ' . ($message->subject ?: 'Votre message')) }}"><i class="ph ph-arrow-bend-up-left mr-2"></i>Répondre</x-button-link>
        <form method="POST" action="{{ route('messages.archive', $message) }}">
            @csrf @method('PUT')
            <x-button type="submit" variant="outline">
                <i class="ph ph-archive mr-2"></i>{{ $message->status === \App\Models\Message::STATUS_ARCHIVED ? 'Désarchiver' : 'Archiver' }}
            </x-button>
        </form>
        <x-admin.delete-button :action="route('messages.destroy', $message)" size="md" confirm="Supprimer ce message ?" />
    </x-slot>
</x-admin.header>

<x-admin.panel class="max-w-4xl">
    <div class="flex flex-wrap items-center gap-x-6 gap-y-1 border-b border-gray-100 pb-4 text-sm">
        <p><span class="text-gray-500">De</span> <span class="font-medium text-gray-900">{{ $message->name }}</span> &lt;<a href="mailto:{{ $message->email }}" class="text-brand-600 hover:underline">{{ $message->email }}</a>&gt;</p>
        @if ($message->phone)<p><span class="text-gray-500">Tél.</span> <a href="tel:{{ $message->phone }}" class="text-gray-900 hover:underline">{{ $message->phone }}</a></p>@endif
        <p class="text-gray-500">{{ $message->created_at->translatedFormat('l j F Y à H:i') }}</p>
    </div>
    <div class="whitespace-pre-line text-gray-800 leading-relaxed">{{ $message->body }}</div>
</x-admin.panel>
@endsection
