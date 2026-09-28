@extends('layouts.app', ['current' => 'admins-index', 'title' => 'Administrateurs'])

@section('content')
    <div class="w-full">
        <x-breadcrumb title="Administrateurs" :items="[
            'Tableau de bord' => route('dashboard'),
            'Admins' => null,
        ]">
        </x-breadcrumb>

        <!-- Filters Section -->
        <div class="bg-white shadow rounded border border-gray-300 p-4 mb-6 mt-6">
            <form action="{{ route('admins.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <x-input name="search" label="Rechercher"
                        placeholder="Rechercher par nom, nom d'utilisateur, email, téléphone..."
                        value="{{ request('search') }}" mb="0" />
                </div>
                <div class="w-1/6">
                    <x-select name="role" label="Rôle" placeholder="Tous" :options="$roles"
                        value="{{ request('role') }}" mb="0" />
                </div>
                <div class="w-1/6">
                    <x-select name="status" label="Statut" placeholder="Tous" :options="$statuses"
                        value="{{ request('status') }}" mb="0" />
                </div>
                <div class="w-1/6">
                    <x-button type="submit" class="w-full">
                        <i class="ph ph-magnifying-glass mr-2 text-lg"></i>
                        Filtrer
                    </x-button>
                </div>
                <div class="w-1/6">
                    <x-button-link href="{{ route('admins.index') }}" variant="outline" class="w-full">
                        <i class="ph ph-x-circle mr-2 text-lg"></i>
                        Reset
                    </x-button-link>
                </div>
            </form>
        </div>

        <div class="w-full">

            <!-- Export Button -->
            <div class="mb-6 flex justify-end space-x-2">

                <x-button-link href="{{ route('admins.create') }}">
                    <i class="ph ph-plus mr-2 text-lg"></i>
                    Nouvel Administrateur
                </x-button-link>

                <x-button-link href="{{ route('admins.audit') }}" variant="outline">
                    <i class="ph ph-clock-counter-clockwise mr-2 text-lg"></i>
                    Journal d'activité
                </x-button-link>
                <x-button-link href="{{ route('admins.export') }}" variant="primary">
                    <i class="ph ph-download mr-2 text-lg"></i>
                    Exporter
                </x-button-link>
            </div>


            <!-- Table Section -->
            <div class="bg-white shadow rounded border border-gray-300 overflow-hidden">
                <table class="admin-table min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                #
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nom
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rôle
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date de création
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @forelse ($admins as $admin)
                            <tr>
                                <td class="px-6 py-2 font-medium">
                                    {{ $loop->iteration }}.
                                </td>
                                <td class="px-6 py-2 font-medium">
                                    <a href="{{ route('admins.show', $admin) }}"
                                        class="text-sm text-brand-600 font-medium hover:underline line-clamp-1">
                                        {{ $admin->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-2">
                                    {{ $admin->email }}
                                </td>
                                <td class="px-6 py-2">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $admin->role_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-2">
                                    {!! $admin->status_badge !!}
                                </td>
                                <td class="px-6 py-2 text-right">
                                    {{ $admin->created_at->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    Aucun administrateur trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                @if ($admins->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200">
                        {{ $admins->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
