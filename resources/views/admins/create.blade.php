@extends('layouts.app', ['current' => 'admins-index', 'title' => 'Nouvel Administrateur'])

@section('content')
    <div class="w-full">
        <x-breadcrumb title="Nouvel Administrateur" :items="[
            'Tableau de bord' => route('dashboard'),
            'Administrateurs' => route('admins.index'),
            'Créer' => null,
        ]">
        </x-breadcrumb>

        <div class="flex-1 flex flex-col lg:flex-row space-y-6 lg:space-y-0 lg:space-x-6 mt-6">

            <!-- Left Column -->
            <div class="w-full lg:w-8/12 space-y-6">

                <x-card title="Créer un Nouvel Administrateur"
                    subtitle="Remplissez les informations ci-dessous pour créer un nouveau compte administrateur.">

                    <form action="{{ route('admins.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <x-input name="name" label="Nom complet" placeholder="Entrez le nom complet"
                                    value="{{ old('name') }}" required mb="0" />
                            </div>

                            <!-- Username -->
                            <div>
                                <x-select name="role" label="Rôle" placeholder="Sélectionnez un rôle" :options="$roles"
                                    value="{{ old('role') }}" required mb="0" />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-input name="email" type="email" label="Email" placeholder="Entrez l'adresse email"
                                    value="{{ old('email') }}" required mb="0" />
                            </div>

                            <!-- Phone -->
                            <div>
                                <x-input name="phone" label="Téléphone" placeholder="Entrez le numéro de téléphone"
                                    value="{{ old('phone') }}" mb="0" />
                            </div>
                        </div>

                        <!-- Information Note -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="ph ph-info text-blue-400 text-lg"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        Information importante
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>
                                            Après la création du compte administrateur :
                                        </p>
                                        <ul class="list-disc list-inside mt-2 space-y-1">
                                            <li>Un mot de passe temporaire sera généré automatiquement</li>
                                            <li>Le compte sera créé avec le statut "Inactif"</li>
                                            <li>Vous devrez envoyer un lien d'activation par email</li>
                                            <li>L'administrateur pourra alors définir son mot de passe et activer son compte
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <x-button-link href="{{ route('admins.index') }}" variant="outline">
                                <i class="ph ph-x mr-2"></i>
                                Annuler
                            </x-button-link>
                            <x-button type="submit">
                                <i class="ph ph-check mr-2"></i>
                                Créer l'Administrateur
                            </x-button>
                        </div>

                    </form>

                </x-card>


            </div>

        </div>
    </div>
@endsection
