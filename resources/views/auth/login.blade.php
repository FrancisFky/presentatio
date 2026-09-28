@extends('layouts.auth')

@section('title', 'Connexion - Admin Ambassade')

@section('heading', 'Espace administration')

@section('subheading', 'Un code de vérification vous sera envoyé par e-mail.')

@section('content')
<form class="space-y-6" action="{{ route('login.store') }}" method="POST" x-data="{ togglePassword(id) { const input = document.getElementById(id); input.type = input.type === 'password' ? 'text' : 'password'; } }">
    @csrf
    
    <x-input 
        type="email"
        name="email"
        label="Adresse email"
        placeholder="Entrez votre email"
        autocomplete="email"
        required
        :error="$errors->first('email')"
    />

    <x-input 
        type="password"
        name="password"
        label="Mot de passe"
        placeholder="Entrez votre mot de passe"
        autocomplete="current-password"
        required
        show-password-toggle
        :error="$errors->first('password')"
    />

    <div class="flex items-center justify-end">
        <div class="text-sm">
            <a href="{{ route('forgot-password') }}" class="font-medium text-brand-600 hover:text-brand-500 transition-colors duration-200">
                Mot de passe oublié ?
            </a>
        </div>
    </div>

    <x-button 
        type="submit"
        variant="primary"
        size="md"
        class="w-full"
        loading-text="Connexion..."
    >
        Se connecter
    </x-button>
</form>

@endsection
