@extends('layouts.admin')

@section('title', 'Connexion - Administration')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-6">
                <img src="{{ Vite::asset('resources/images/logo_explo_space.webp') }}" alt="{{ config('app.name') }}" class="h-14 w-auto">
            </div>
            <h1 class="text-2xl font-semibold text-gray-900 mb-1">
                Connexion administrateur
            </h1>
            <p class="text-sm text-gray-500">
                Accédez au panneau d'administration Explo.space
            </p>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Formulaire Livewire -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            @livewire('admin.auth.admin-login')
        </div>

        <!-- Lien retour site public -->
        <div class="text-center mt-6">
            <a href="/" class="inline-flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors font-medium">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour au site
            </a>
        </div>
    </div>
</div>
@endsection
