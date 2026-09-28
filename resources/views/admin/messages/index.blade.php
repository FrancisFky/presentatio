@extends('layouts.app')
@section('title', 'Messages - Admin Ambassade')

@section('content')
<x-admin.header title="Messages" subtitle="Reçus par le formulaire de contact du site." :items="['Tableau de bord' => route('dashboard'), 'Messages' => null]" />

<x-admin.filters placeholder="Nom, e-mail, objet…">
    <x-admin.filter-select name="status" :options="\App\Models\Message::STATUSES" placeholder="Boîte de réception" />
</x-admin.filters>

@if ($messages->isEmpty())
    <x-admin.empty icon="ph-envelope-simple-open" title="Aucun message" />
@else
    <div class="divide-y divide-gray-100 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        @foreach ($messages as $message)
            @php $unread = $message->status === \App\Models\Message::STATUS_UNREAD; @endphp
            <a href="{{ route('messages.show', $message) }}" class="flex items-start gap-4 px-4 py-3 hover:bg-gray-50 {{ $unread ? 'bg-brand-50/40' : '' }}">
                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $unread ? 'bg-gold-500' : 'bg-transparent' }}"></span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-baseline justify-between gap-3">
                        <p class="truncate {{ $unread ? 'font-semibold text-gray-900' : 'text-gray-700' }}">{{ $message->name }}</p>
                        <span class="shrink-0 text-xs text-gray-400">{{ $message->created_at->translatedFormat('d M, H:i') }}</span>
                    </div>
                    <p class="truncate text-sm {{ $unread ? 'font-medium text-gray-800' : 'text-gray-600' }}">{{ $message->subject ?: 'Sans objet' }}</p>
                    <p class="truncate text-sm text-gray-500">{{ Str::limit($message->body, 120) }}</p>
                </div>
            </a>
        @endforeach
    </div>
    <div class="mt-4">{{ $messages->links() }}</div>
@endif
@endsection
