<!DOCTYPE html>
<html lang="fr" class="h-full admin-page">
<head>
    @include('partials.admin-head')
</head>
<body class="h-full admin-page">
    <div class="flag-stripe h-1.5 w-full"></div>
    <div class="min-h-full flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <img src="{{ asset('images/armoiries.svg') }}" alt="Armoiries de la République du Congo" class="mx-auto h-20 w-20 object-contain">
            <p class="mt-3 text-xs font-semibold uppercase tracking-[0.2em] text-gold-600">Ambassade du Congo au Kenya</p>
            <h2 class="mt-2 font-display text-3xl font-semibold text-brand-700">
                @yield('heading', 'Espace administration')
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                @yield('subheading', 'Connectez-vous à votre compte')
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-xl rounded-xl sm:px-10 border border-gray-100">
                @include('partials.notifications')
                @yield('content')
            </div>
            <p class="mt-6 text-center text-sm">
                <a href="{{ url('/') }}" class="text-gray-500 hover:text-brand-600"><i class="ph ph-arrow-left mr-1"></i>Retour au site</a>
            </p>
        </div>
    </div>

    @stack('scripts')
    @livewireScripts
</body>
</html>
