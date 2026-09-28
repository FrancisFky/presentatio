@extends('layouts.app', ['current' => 'profile', 'title' => 'Mon Profil'])

@section('content')
    <div class="h-full w-full">

        <x-breadcrumb title="Mon Profil" :items="[
            'Tableau de bord' => route('dashboard'),
            'Profil' => null,
        ]">
        </x-breadcrumb>

        <main x-data="{
            showChangePasswordModal: false,
        }">

            <div class="flex-1 flex flex-col lg:flex-row space-y-6 lg:space-y-0 lg:space-x-6 mt-6">

                <!-- Left Column -->
                <div class="w-full lg:w-8/12 space-y-6">

                    <!-- PROFILE INFORMATION CARD -->
                    <x-card title="Informations Personnelles" subtitle="Gérez vos informations de profil">
                        
                        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <x-input 
                                        name="name" 
                                        label="Nom complet" 
                                        placeholder="Entrez votre nom complet"
                                        value="{{ old('name', $admin->name) }}"
                                        required
                                        mb="0" 
                                    />
                                </div>

                                <!-- Role -->
                                <div>
                                    <x-select 
                                        name="role" 
                                        label="Rôle" 
                                        placeholder="Sélectionnez un rôle"
                                        :options="$roles"
                                        value="{{ old('role', $admin->role) }}"
                                        required
                                        mb="0" 
                                        disabled
                                    />
                                </div>

                                <!-- Email -->
                                <div>
                                    <x-input 
                                        name="email" 
                                        type="email"
                                        label="Email" 
                                        placeholder="Entrez votre adresse email"
                                        value="{{ old('email', $admin->email) }}"
                                        required
                                        mb="0" 
                                        disabled
                                    />
                                </div>

                                <!-- Phone -->
                                <div>
                                    <x-input 
                                        name="phone" 
                                        label="Téléphone" 
                                        placeholder="Entrez votre numéro de téléphone"
                                        value="{{ old('phone', $admin->phone) }}"
                                        mb="0" 
                                    />
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end pt-6 border-t border-gray-200">
                                <x-button type="submit">
                                    <i class="ph ph-check mr-2"></i>
                                    Mettre à Jour le Profil
                                </x-button>
                            </div>

                        </form>
                    </x-card>

                    <!-- SECURITY CARD -->
                    <x-card title="Sécurité" subtitle="Gérez votre mot de passe et paramètres de sécurité">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">
                                        Mot de passe
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        Changez votre mot de passe pour sécuriser votre compte
                                    </p>
                                </div>
                                <x-button @click="showChangePasswordModal = true" variant="outline">
                                    <i class="ph ph-key mr-2"></i>
                                    Changer le Mot de Passe
                                </x-button>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">
                                        Dernière connexion
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        @if($admin->last_activity_at)
                                            {{ $admin->last_activity_at->format('d/m/Y à H:i') }}
                                        @else
                                            Aucune activité enregistrée
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </x-card>

                </div>
                {{-- end left column --}}

                <!-- Right Sidebar -->
                <div class="w-full lg:w-4/12 space-y-6">

                    <!-- Profile Status Card -->
                    <x-card title="Statut du Compte" subtitle="Informations sur votre compte">
                        <x-slot name="action">
                            {!! $admin->status_badge !!}
                        </x-slot>
                        <ul class="text-gray-600 space-y-3">
                            <li class="flex justify-between">
                                <span>Rôle</span>
                                <span class="font-medium">{{ $admin->role_name }}</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Membre depuis</span>
                                <span>{{ $admin->created_at->format('d/m/Y') }}</span>
                            </li>
                            <li class="flex justify-between">
                                <span>ID Utilisateur</span>
                                <span class="font-mono text-xs">#{{ $admin->id }}</span>
                            </li>
                        </ul>

                        @if ($admin->updated_at)
                            <p class="text-gray-600 text-sm mt-4">
                                Profil mis à jour: {{ $admin->updated_at->diffForHumans() }}
                            </p>
                        @endif
                    </x-card>

                    <!-- Permissions Card -->
                    <x-card title="Permissions" subtitle="Vos droits d'accès">
                        <div class="space-y-3">
                            <div class="text-xs text-gray-500 space-y-1">
                                @switch($admin->role)
                                    @case(\App\Models\Admin::ROLE_SUPER_ADMIN)
                                        <div class="flex items-center">
                                            <i class="ph ph-check-circle text-green-500 mr-2"></i>
                                            <span>Accès complet au système</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="ph ph-check-circle text-green-500 mr-2"></i>
                                            <span>Gestion des administrateurs</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="ph ph-check-circle text-green-500 mr-2"></i>
                                            <span>Configuration système</span>
                                        </div>
                                        @break
                                    @case(\App\Models\Admin::ROLE_ADMIN)
                                        <div class="flex items-center">
                                            <i class="ph ph-check-circle text-green-500 mr-2"></i>
                                            <span>Gestion des utilisateurs</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="ph ph-check-circle text-green-500 mr-2"></i>
                                            <span>Gestion des marchands</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="ph ph-check-circle text-green-500 mr-2"></i>
                                            <span>Consultation des rapports</span>
                                        </div>
                                        @break
                                    @case(\App\Models\Admin::ROLE_MEMBER)
                                        <div class="flex items-center">
                                            <i class="ph ph-check-circle text-green-500 mr-2"></i>
                                            <span>Consultation des données</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="ph ph-check-circle text-green-500 mr-2"></i>
                                            <span>Support utilisateurs</span>
                                        </div>
                                        @break
                                @endswitch
                            </div>
                        </div>
                    </x-card>

                </div>
                {{-- end right sidebar --}}

            </div>

            <!-- Change Password Modal -->
            <x-modal show="showChangePasswordModal" title="Changer le Mot de Passe">
                <form action="{{ route('password.change') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div>
                        <x-input 
                            name="current_password" 
                            type="password"
                            label="Mot de passe actuel" 
                            placeholder="Entrez votre mot de passe actuel"
                            required
                            mb="0" 
                        />
                    </div>

                    <!-- New Password -->
                    <div>
                        <x-input 
                            name="password" 
                            type="password"
                            label="Nouveau mot de passe" 
                            placeholder="Entrez votre nouveau mot de passe"
                            required
                            mb="0" 
                        />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input 
                            name="password_confirmation" 
                            type="password"
                            label="Confirmer le mot de passe" 
                            placeholder="Confirmez votre nouveau mot de passe"
                            required
                            mb="0" 
                        />
                    </div>

                    <!-- Password Requirements -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">
                            Exigences du mot de passe :
                        </h4>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• Au moins 8 caractères</li>
                            <li>• Contenir au moins une lettre majuscule</li>
                            <li>• Contenir au moins une lettre minuscule</li>
                            <li>• Contenir au moins un chiffre</li>
                            <li>• Contenir au moins un caractère spécial</li>
                        </ul>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <x-button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2">
                            <i class="ph ph-key mr-2"></i>
                            Changer le Mot de Passe
                        </x-button>
                    </div>
                </form>
            </x-modal>

        </main>
    </div>
@endsection