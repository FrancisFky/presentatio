@extends('layouts.app')
@section('title', 'Pages - Admin Ambassade')

@section('content')
<x-admin.header title="Pages" subtitle="Les pages du menu « À propos » du site." :items="['Tableau de bord' => route('dashboard'), 'Pages' => null]" />

<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    @foreach ($pages as $page)
        <a href="{{ route('pages.edit', $page) }}" class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:border-gold-300 hover:shadow">
            <div class="h-32 bg-brand-700 bg-cover bg-center" @if ($page->image_path) style="background-image: url('{{ Storage::disk('public')->url($page->image_path) }}')" @endif></div>
            <div class="p-4">
                <p class="font-semibold text-gray-900 group-hover:text-brand-600">{{ $page->title_fr }}</p>
                <p class="text-sm text-gray-500">/{{ $page->slug }}</p>
                <div class="mt-3 flex items-center gap-2">
                    <x-status-badge :status="$page->status" :labels="\App\Models\Page::STATUSES" />
                    <x-admin.lang-status :model="$page" field="body" />
                    <span class="ml-auto text-xs text-gray-400">Modifiée {{ $page->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </a>
    @endforeach
</div>
@endsection
