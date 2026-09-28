@extends('layouts.app')
@section('title', 'Actualités - Admin Ambassade')

@section('content')
<x-admin.header title="Actualités" :items="['Tableau de bord' => route('dashboard'), 'Actualités' => null]">
    <x-slot name="action">
        <x-button-link href="{{ route('news.create') }}"><i class="ph ph-plus mr-2"></i>Nouvel article</x-button-link>
    </x-slot>
</x-admin.header>

<div class="grid gap-6 xl:grid-cols-[1fr_20rem]">
    <div>
        <x-admin.filters placeholder="Titre de l'article…">
            <x-admin.filter-select name="status" :options="\App\Models\News::STATUSES" placeholder="Tous les statuts" />
            <x-admin.filter-select name="category" :options="$categories->pluck('name_fr', 'id')" placeholder="Toutes les rubriques" />
        </x-admin.filters>

        @if ($news->isEmpty())
            <x-admin.empty icon="ph-newspaper" title="Aucun article" text="Les articles publiés apparaissent sur l'accueil et dans la rubrique Actualités du site." />
        @else
            <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
                <table class="admin-table min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <tr><th class="px-4 py-3"></th><th class="px-4 py-3">Titre</th><th class="px-4 py-3">Rubrique</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Statut</th><th class="px-4 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($news as $article)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-3">
                                    @if ($article->image_path)
                                        <img src="{{ Storage::disk('public')->url($article->image_path) }}" class="h-10 w-14 rounded object-cover" alt="">
                                    @else
                                        <span class="flex h-10 w-14 items-center justify-center rounded bg-gray-100"><i class="ph ph-image text-gray-300"></i></span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('news.edit', $article) }}" class="font-medium text-gray-900 hover:text-brand-600">{{ $article->title_fr ?: $article->title_en }}</a>
                                    <div class="mt-1"><x-admin.lang-status :model="$article" /></div>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $article->category?->name_fr ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $article->published_on->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <x-status-badge :status="$article->published_on->isFuture() && $article->isPublished() ? 'scheduled' : $article->status"
                                        :labels="\App\Models\News::STATUSES + ['scheduled' => 'Programmé']" />
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        @if ($article->isPublished())
                                            <x-button-link href="{{ route('site.news.show', ['locale' => 'fr', 'news' => $article]) }}" target="_blank" variant="ghost" size="xs" title="Voir sur le site"><i class="ph ph-arrow-square-out"></i></x-button-link>
                                        @endif
                                        <x-button-link href="{{ route('news.edit', $article) }}" variant="ghost" size="xs" title="Modifier"><i class="ph ph-pencil-simple"></i></x-button-link>
                                        <x-admin.delete-button :action="route('news.destroy', $article)" confirm="Supprimer cet article ?" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $news->links() }}</div>
        @endif
    </div>

    {{-- Rubriques --}}
    <aside x-data="{ lang: 'fr' }">
        <x-admin.panel title="Rubriques" description="Pour classer les articles.">
            <ul class="divide-y divide-gray-100 -my-2">
                @forelse ($categories as $category)
                    <li class="flex items-center justify-between gap-2 py-2" x-data="{ editing: false }">
                        <div x-show="!editing" class="min-w-0">
                            <p class="truncate text-sm font-medium text-gray-800">{{ $category->name_fr }}</p>
                            <p class="truncate text-xs text-gray-500">{{ $category->name_en ?: '—' }} · {{ $category->news_count }} article(s)</p>
                        </div>
                        <form x-show="editing" x-cloak method="POST" action="{{ route('news-categories.update', $category) }}" class="flex-1 space-y-1">
                            @csrf @method('PUT')
                            <input name="name_fr" value="{{ $category->name_fr }}" required class="block h-8 w-full rounded border-gray-300 text-sm" placeholder="Français">
                            <input name="name_en" value="{{ $category->name_en }}" class="block h-8 w-full rounded border-gray-300 text-sm" placeholder="English">
                            <x-button type="submit" size="xs">Enregistrer</x-button>
                        </form>
                        <div class="flex shrink-0 items-center">
                            <x-button type="button" variant="ghost" size="xs" @click="editing = !editing" title="Renommer"><i class="ph ph-pencil-simple"></i></x-button>
                            <x-admin.delete-button :action="route('news-categories.destroy', $category)" confirm="Supprimer cette rubrique ? Ses articles resteront, sans rubrique." />
                        </div>
                    </li>
                @empty
                    <li class="py-2 text-sm text-gray-500">Aucune rubrique.</li>
                @endforelse
            </ul>
            <form method="POST" action="{{ route('news-categories.store') }}" class="space-y-2 border-t border-gray-100 pt-4">
                @csrf
                <input name="name_fr" required placeholder="Nouvelle rubrique (FR)" class="block h-9 w-full rounded-lg border-gray-300 text-sm">
                <input name="name_en" placeholder="English name" class="block h-9 w-full rounded-lg border-gray-300 text-sm">
                <x-button type="submit" size="sm" variant="outline" class="w-full"><i class="ph ph-plus mr-1"></i>Ajouter</x-button>
            </form>
        </x-admin.panel>
    </aside>
</div>
@endsection
