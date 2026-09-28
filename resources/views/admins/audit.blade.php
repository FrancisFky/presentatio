@extends('layouts.app', ['current' => 'admins-audit', 'title' => 'Journal d\'activité'])

@section('content')
    <div class="w-full">
        <x-breadcrumb title="Journal d'activité" :items="[
            'Tableau de bord' => route('dashboard'),
            'Administrateurs' => route('admins.index'),
            'Journal d\'activité' => null,
        ]" />

        <div class="bg-white shadow rounded border border-gray-300 p-4 mb-6 mt-6">
            <form action="{{ route('admins.audit') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <x-input name="search" label="Action" placeholder="subscriptions.codes, merchants.verify…"
                        value="{{ request('search') }}" mb="0" />
                </div>
                <div class="md:w-1/4">
                    <x-select name="admin" label="Administrateur" placeholder="Tous" :options="$admins"
                        value="{{ request('admin') }}" mb="0" />
                </div>
                <div class="md:w-1/6">
                    <x-button type="submit" class="w-full"><i class="ph ph-magnifying-glass mr-2 text-lg"></i>Filtrer</x-button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded border border-gray-300">
            <table class="admin-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Administrateur</th>
                        <th class="px-6 py-3">Action</th>
                        <th class="px-6 py-3">Sur</th>
                        <th class="px-6 py-3">Résultat</th>
                        <th class="px-6 py-3">Adresse IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse ($logs as $log)
                        <tr>
                            <td class="px-6 py-2 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-6 py-2">{{ $log->admin?->name ?? 'Supprimé' }}</td>
                            <td class="px-6 py-2"><code class="text-xs">{{ $log->method }} {{ $log->action }}</code></td>
                            <td class="px-6 py-2 text-xs text-gray-600">{{ collect($log->targets ?? [])->implode(', ') ?: '—' }}</td>
                            <td class="px-6 py-2">
                                <span @class([
                                    'inline-flex rounded-full px-2 py-0.5 text-xs font-medium',
                                    'bg-green-100 text-green-800' => $log->status < 400,
                                    'bg-red-100 text-red-800' => $log->status >= 400,
                                ])>{{ $log->status < 400 ? 'Fait' : 'Refusé (' . $log->status . ')' }}</span>
                            </td>
                            <td class="px-6 py-2 text-xs text-gray-500">{{ $log->ip }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Aucune action enregistrée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $logs->links('vendor.pagination.tailwind') }}</div>
    </div>
@endsection
