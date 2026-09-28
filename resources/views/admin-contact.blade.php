@extends('layouts.empty', ['title' => 'Compte Désactivé'])

@section('content')
<div class="my-12 flex h-screen justify-center items-center">

    <div class="w-full lg:w-2/3">
        <x-card>
    
            <div class="sm:mx-auto sm:w-full sm:max-w-md mb-6">
                <div class="text-center">
                    <div class="mx-auto h-16 w-16 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="ph ph-x-circle text-red-600 text-3xl"></i>
                    </div>
                    <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                        Compte Désactivé
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Votre compte administrateur a été désactivé
                    </p>
                </div>
            </div>
    
            <div >
                <!-- Contact Information -->
                <div class="space-y-6">
                    <div>
                        <p class="text-sm text-gray-600">
                            Si vous pensez que c'est une erreur ou si vous souhaitez obtenir plus d'informations,
                            veuillez contacter l'administration :
                        </p>
                    </div>
    
                    <!-- Contact Methods -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0">
                                <i class="ph ph-envelope text-gray-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Email</p>
                                <a href="mailto:{{ config('mail.from.address') }}" class="text-sm text-brand-600 hover:text-brand-500">
                                    {{ config('mail.from.address') }}
                                </a>
                            </div>
                        </div>
    
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0">
                                <i class="ph ph-phone text-gray-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Téléphone</p>
                                <a href="tel:+243000000000" class="text-sm text-brand-600 hover:text-brand-500">
                                    +243 000 000 000
                                </a>
                            </div>
                        </div>
                        
                    </div>
    
                    <!-- Logout Button -->
                    <div class="pt-4 flex justify-center">
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <x-button type="submit" variant="primary">
                                <i class="ph ph-sign-out mr-2"></i>
                                Se Déconnecter
                            </x-button>
                        </form>
                    </div>
                </div>

            </div>
    
        </x-card>
    </div>

</div>
@endsection
