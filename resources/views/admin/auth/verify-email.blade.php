@extends('layouts.admin')

@section('title', 'Vérification email - Administration')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="flex justify-center mb-4">
                <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                Vérifiez votre email
            </h1>
            <p class="text-gray-600">
                Un email de vérification a été envoyé
            </p>
        </div>

        <!-- Content Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8">
            <!-- Success message -->
            @if (session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                    <div class="flex">
                        <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Main message -->
            <div class="mb-6">
                <p class="text-gray-700 text-center leading-relaxed mb-4">
                    Avant de continuer, veuillez vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer.
                </p>
                <p class="text-sm text-gray-600 text-center">
                    Si vous n'avez pas reçu l'email, nous pouvons vous en envoyer un autre.
                </p>
            </div>

            <!-- Resend form -->
            <form method="POST" action="{{ route('admin.verification.resend') }}">
                @csrf
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Renvoyer l'email de vérification
                </button>
            </form>

            <!-- Info box -->
            <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <p class="ml-3 text-sm text-blue-700">
                        Le lien de vérification expire dans 60 minutes pour des raisons de sécurité.
                    </p>
                </div>
            </div>
        </div>

        <!-- Logout link -->
        <div class="text-center">
            <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Se déconnecter
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
