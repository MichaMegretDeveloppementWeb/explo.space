@extends('layouts.admin')

@section('title', 'Mot de passe oublié - Administration')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-6">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-2xl font-semibold text-gray-900 mb-1">
                Mot de passe oublié
            </h1>
            <p class="text-sm text-gray-500">
                Réinitialisez votre mot de passe administrateur
            </p>
        </div>

        <!-- Content Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <!-- Info message -->
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
                <p class="text-sm text-blue-700 flex items-start">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <span>Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</span>
                </p>
            </div>

            <!-- Livewire Component -->
            @livewire('admin.auth.forgot-password-form')
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
