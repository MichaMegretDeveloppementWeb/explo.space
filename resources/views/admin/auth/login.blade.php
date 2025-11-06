@extends('layouts.admin')

@section('title', 'Connexion - Administration')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="flex justify-center mb-4">
                <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L13.09 8.26L22 9L13.09 9.74L12 16L10.91 9.74L2 9L10.91 8.26L12 2Z"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                Administration
            </h1>
            <p class="text-gray-600">
                Explo.space - Connexion administrateur
            </p>
        </div>

        <!-- Formulaire Livewire -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8">
            @livewire('admin.auth.admin-login')
        </div>

        <!-- Lien retour site public -->
        <div class="text-center">
            <a href="/" class="inline-flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour au site
            </a>
        </div>
    </div>
</div>
@endsection
