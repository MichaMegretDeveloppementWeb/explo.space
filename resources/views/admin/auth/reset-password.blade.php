@extends('layouts.admin')

@section('title', 'Réinitialiser le mot de passe - Administration')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-6">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-2xl font-semibold text-gray-900 mb-1">
                Nouveau mot de passe
            </h1>
            <p class="text-sm text-gray-500">
                Choisissez un mot de passe sécurisé pour votre compte
            </p>
        </div>

        <!-- Content Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <!-- Livewire Component -->
            @livewire('admin.auth.reset-password-form', ['token' => $token, 'email' => $email])
        </div>

        <!-- Lien retour connexion -->
        <div class="text-center mt-6">
            <a href="{{ route('admin.login') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors font-medium">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour à la connexion
            </a>
        </div>
    </div>
</div>
@endsection
