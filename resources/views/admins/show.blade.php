@extends('layouts.app', ['current' => 'admins-index', 'title' => 'Administrateur'])

@section('content')
    <div class="h-full w-full">

        <x-breadcrumb title="Administrateur" :items="[
            'Tableau de bord' => route('dashboard'),
            'Administrateurs' => route('admins.index'),
            $admin->name => null,
        ]">
        </x-breadcrumb>

        <main x-data="{
            showSendActivationModal: false,
            showDeactivateModal: false,
            showReactivateModal: false,
            showDeleteModal: false,
        }">

            <div class="flex-1 flex flex-col lg:flex-row space-y-6 lg:space-y-0 lg:space-x-6 mt-6">

                <!-- Left Column -->
                <div class="w-full lg:w-8/12 space-y-6">

                    <!-- ADMIN INFORMATION CARD -->
                    <x-card title="Informations" subtitle="Informations de base sur l'administrateur">
                        <ul class="text-gray-600 text-sm space-y-2">
                            <li><strong>Nom:</strong> {{ $admin->name ?? 'N/A' }}</li>
                            <li><strong>Nom d'utilisateur:</strong> {{ $admin->username ?? 'N/A' }}</li>
                            <li><strong>Email:</strong> {{ $admin->email ?? 'N/A' }}</li>
                            <li><strong>Téléphone:</strong> {{ $admin->phone ?? 'N/A' }}</li>
                            <li><strong>Rôle:</strong> {{ $admin->role_name ?? 'N/A' }}</li>
                        </ul>
                    </x-card>

                    <!-- ACTIVATION STATUS CARD -->
                    @if (!$admin->isActive())
                        <x-card title="Statut d'Activation" subtitle="Informations sur l'activation du compte">
                            @if ($admin->hasActivationTokenSent())
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="ph ph-clock text-yellow-400 text-lg"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800">
                                                Lien d'activation envoyé
                                            </h3>
                                            <div class="mt-2 text-sm text-yellow-700">
                                                <p>
                                                    Un lien d'activation a été envoyé à {{ $admin->email }} 
                                                    le {{ $admin->activation_sent_at->format('d/m/Y à H:i') }}.
                                                </p>
                                                <p class="mt-1">
                                                    L'administrateur doit utiliser ce lien pour activer son compte et définir son mot de passe.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="ph ph-warning text-red-400 text-lg"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800">
                                                Compte non activé
                                            </h3>
                                            <div class="mt-2 text-sm text-red-700">
                                                <p>
                                                    Ce compte administrateur n'a pas encore été activé. 
                                                    Vous devez envoyer un lien d'activation.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </x-card>
                    @endif

                    <!-- ACTIONS CARD -->
                    <x-card title="Actions" subtitle="Actions disponibles pour l'administrateur">
                        <div class="flex flex-wrap gap-3">
                            @if (!$admin->isActive())
                                <x-button @click="showSendActivationModal = true">
                                    <i class="ph ph-envelope mr-2"></i>
                                    Envoyer Lien d'Activation
                                </x-button>
                            @endif

                            @if ($admin->status == \App\Models\Admin::STATUS_DEACTIVATED)
                                <x-button @click="showReactivateModal = true" variant="success">
                                    <i class="ph ph-check-circle mr-2"></i>
                                    Réactiver
                                </x-button>
                            @endif

                            @if ($admin->isActive())
                                <x-button @click="showDeactivateModal = true" variant="danger">
                                    <i class="ph ph-x-square mr-2"></i>
                                    Désactiver
                                </x-button>
                            @endif

                            <x-button @click="showDeleteModal = true" variant="outline" class="border-red-300 text-red-700 hover:bg-red-50">
                                <i class="ph ph-trash mr-2"></i>
                                Supprimer
                            </x-button>
                        </div>
                    </x-card>

                </div>
                {{-- end left column --}}

                <!-- Right Sidebar -->
                <div class="w-full lg:w-4/12 space-y-6">

                    <!-- Status Card -->
                    <x-card title="Statut" subtitle="Statut de l'administrateur">
                        <x-slot name="action">
                            {!! $admin->status_badge !!}
                        </x-slot>
                        <ul class="text-gray-600 space-y-3">
                            <li class="flex justify-between">
                                <span>Créé le</span>
                                <span>{{ $admin->created_at->format('d/m/Y') }}</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Dernière activité</span>
                                <span>
                                    @if ($admin->last_activity_at)
                                        {{ $admin->last_activity_at->diffForHumans() }}
                                    @else
                                        Jamais
                                    @endif
                                </span>
                            </li>
                        </ul>

                        @if ($admin->updated_at)
                            <p class="text-gray-600 text-sm mt-4">Dernière mise à jour:
                                {{ $admin->updated_at->diffForHumans() }}</p>
                        @endif
                    </x-card>

                    <!-- Role Information Card -->
                    <x-card title="Permissions" subtitle="Rôle et permissions">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Rôle:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $admin->role_name }}
                                </span>
                            </div>
                            
                            <div class="text-xs text-gray-500 space-y-1">
                                @switch($admin->role)
                                    @case(\App\Models\Admin::ROLE_SUPER_ADMIN)
                                        <p>• Accès complet à toutes les fonctionnalités</p>
                                        <p>• Gestion des administrateurs</p>
                                        <p>• Configuration système</p>
                                        @break
                                    @case(\App\Models\Admin::ROLE_ADMIN)
                                        <p>• Gestion des utilisateurs</p>
                                        <p>• Gestion des marchands</p>
                                        <p>• Consultation des rapports</p>
                                        @break
                                    @case(\App\Models\Admin::ROLE_MEMBER)
                                        <p>• Consultation des données</p>
                                        <p>• Support utilisateurs</p>
                                        @break
                                @endswitch
                            </div>
                        </div>
                    </x-card>

                </div>
                {{-- end right sidebar --}}

            </div>

            <!-- Modals -->
            
            <!-- Send Activation Modal -->
            @if (!$admin->isActive())
                <x-modal show="showSendActivationModal" title="Envoyer Lien d'Activation">
                    <form action="{{ route('admins.send-activation', $admin) }}" method="POST" class="space-y-6">
                        @csrf

                        <p class="text-gray-600 text-sm">
                            Êtes-vous sûr de vouloir envoyer un lien d'activation à <strong>{{ $admin->email }}</strong> ?
                            <br><br>
                            L'administrateur recevra un email avec un lien pour activer son compte et définir son mot de passe.
                        </p>

                        <div class="flex justify-end space-x-3">
                            <x-button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2">
                                <i class="ph ph-envelope mr-2"></i>
                                Envoyer
                            </x-button>
                        </div>
                    </form>
                </x-modal>
            @endif

            <!-- Deactivate Modal -->
            @if ($admin->isActive())
                <x-modal show="showDeactivateModal" title="Désactiver l'administrateur">
                    <form action="{{ route('admins.deactivate', $admin) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <p class="text-gray-600 text-sm">
                            Êtes-vous sûr de vouloir désactiver cet administrateur?
                            <br><br>
                            Une fois désactivé, l'administrateur ne pourra plus se connecter à son compte et sera redirigé vers une page de contact.
                        </p>

                        <div class="flex justify-end space-x-3">
                            <x-button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2">
                                <i class="ph ph-x-square mr-2"></i>
                                Désactiver
                            </x-button>
                        </div>
                    </form>
                </x-modal>
            @endif

            <!-- Reactivate Modal -->
            @if ($admin->status == \App\Models\Admin::STATUS_DEACTIVATED)
                <x-modal show="showReactivateModal" title="Réactiver l'administrateur">
                    <form action="{{ route('admins.reactivate', $admin) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <p class="text-gray-600 text-sm">
                            Êtes-vous sûr de vouloir réactiver cet administrateur?
                            <br><br>
                            Une fois réactivé, l'administrateur pourra à nouveau se connecter à son compte.
                        </p>

                        <div class="flex justify-end space-x-3">
                            <x-button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2">
                                <i class="ph ph-check-circle mr-2"></i>
                                Réactiver
                            </x-button>
                        </div>
                    </form>
                </x-modal>
            @endif

            <!-- Delete Modal -->
            <x-modal show="showDeleteModal" title="Supprimer l'administrateur">
                <form action="{{ route('admins.destroy', $admin) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('DELETE')

                    <p class="text-gray-600 text-sm">
                        <strong>Attention:</strong> Cette action est irréversible!
                        <br><br>
                        Êtes-vous sûr de vouloir supprimer définitivement cet administrateur?
                        Toutes les données associées seront perdues.
                    </p>

                    <div class="flex justify-end space-x-3">
                        <x-button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2">
                            <i class="ph ph-trash mr-2"></i>
                            Supprimer Définitivement
                        </x-button>
                    </div>
                </form>
            </x-modal>

        </main>
    </div>
@endsection