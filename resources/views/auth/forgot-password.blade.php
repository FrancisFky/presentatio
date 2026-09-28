@extends('layouts.auth')

@section('title', 'Mot de passe oublié - Admin Ambassade')

@section('heading', 'Forgot your password?')

@section('subheading', 'Enter your email address and we\'ll send you a link to reset your password.')

@section('content')
<form class="space-y-6" action="{{ route('forgot-password.store') }}" method="POST" x-data="{ togglePassword(id) { const input = document.getElementById(id); input.type = input.type === 'password' ? 'text' : 'password'; } }">
    @csrf
    
    <x-input 
        type="email"
        name="email"
        label="Email address"
        placeholder="Enter your email address"
        autocomplete="email"
        required
        :error="$errors->first('email')"
    />

    <x-button 
        type="submit"
        variant="primary"
        size="md"
        class="w-full"
        loading-text="Sending..."
    >
        Send reset link
    </x-button>

    <div class="text-center">
        <a href="{{ route('login') }}" class="text-sm font-medium text-brand-600 hover:text-brand-500 transition-colors duration-200">
            Back to login
        </a>
    </div>
</form>
@endsection
