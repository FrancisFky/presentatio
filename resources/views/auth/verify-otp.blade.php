@extends('layouts.auth')

@section('heading', 'Vérification OTP')
@section('subheading', 'Entrez le code de vérification envoyé à votre email')

@section('content')
    @if (\App\Models\Admin::testOtp())
        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <i class="ph ph-flask mr-1"></i>Développement : le code est <strong class="font-mono">{{ \App\Models\Admin::testOtp() }}</strong>.
        </div>
    @endif

    <form class="space-y-6 mb-3" action="{{ route('otp.verify.store') }}" method="POST">
        @csrf

        <x-input type="text" name="otp" label="Code de vérification" placeholder="Entrez le code de vérification"
            required :error="$errors->first('otp')" />


        <x-button type="submit" variant="primary" size="md" class="w-full">
            Vérifier et continuer
        </x-button>
    </form>

    <div class="flex  gap-3 mt-6">

        <div class="w-1/2 mx-auto">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-gray-600 hover:text-gray-800">
                    <i class="ph ph-arrow-left mr-1"></i>
                    Retour à l'accueil
                </button>
            </form>            
        </div>


        <div class="w-1/2 mx-auto">
            @if (Auth::guard('admin')->user()->otp_expires_at < now())
                <form action="{{ route('otp.resend') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-gray-600 hover:text-gray-800">
                        <i class="ph ph-arrow-left mr-1"></i>
                        Renvoyer le code
                    </button>
                </form>
            @endif
        </div>


    </div>
@endsection
