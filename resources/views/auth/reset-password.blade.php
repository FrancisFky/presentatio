@extends('layouts.auth')

@section('title', 'Nouveau mot de passe - Admin Ambassade')

@section('heading', 'Reset your password')

@section('subheading', 'Enter your new password below.')

@section('content')
<form class="space-y-6" action="{{ route('password.update') }}" method="POST" x-data="{ togglePassword(id) { const input = document.getElementById(id); input.type = input.type === 'password' ? 'text' : 'password'; } }">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    
    <x-input 
        type="email"
        name="email"
        label="Email address"
        placeholder="Enter your email address"
        autocomplete="email"
        :value="old('email', $email ?? null)"
        required
        :error="$errors->first('email')"
    />

    <x-input 
        type="password"
        name="password"
        label="New Password"
        placeholder="Enter your new password"
        autocomplete="new-password"
        required
        show-password-toggle
        :error="$errors->first('password')"
    />

    <x-input 
        type="password"
        name="password_confirmation"
        label="Confirm New Password"
        placeholder="Confirm your new password"
        autocomplete="new-password"
        required
        :error="$errors->first('password_confirmation')"
    />

    <x-button 
        type="submit"
        variant="primary"
        size="md"
        class="w-full"
        loading-text="Resetting..."
    >
        Reset Password
    </x-button>

    <div class="text-center">
        <a href="{{ route('login') }}" class="text-sm font-medium text-brand-600 hover:text-brand-500 transition-colors duration-200">
            Back to login
        </a>
    </div>
</form>
@endsection
