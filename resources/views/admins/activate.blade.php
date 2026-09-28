@extends('layouts.empty', ['title' => 'Activer votre compte'])

@section('content')
    <div class=" bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Activer votre compte
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Définissez un mot de passe pour activer votre compte administrateur
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                
                <!-- Admin Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="ph ph-user text-blue-400 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">
                                Informations du compte
                            </h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p><strong>Nom:</strong> {{ $admin->name }}</p>
                                <p><strong>Email:</strong> {{ $admin->email }}</p>
                                <p><strong>Rôle:</strong> {{ $admin->role_name }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activation Form -->
                <form action="{{ route('admin.process-activation', $token) }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Password -->
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

                    <!-- Submit Button -->
                    <div>
                        <x-button type="submit" class="w-full">
                            <i class="ph ph-check-circle mr-2"></i>
                            Activer mon compte
                        </x-button>
                    </div>

                </form>

                <!-- Support Link -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-600">
                        Problème avec l'activation ? 
                        <a href="mailto:{{ config('mail.from.address') }}" class="font-medium text-brand-600 hover:text-brand-500">
                            Contactez le support
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>
@endsection