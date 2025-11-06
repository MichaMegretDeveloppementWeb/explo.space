@extends('layouts.web')

@push('head')
    {{-- Google reCAPTCHA v3 --}}
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}"></script>

    {{-- Meta tags pour JavaScript --}}
    <meta name="recaptcha-error-message" content="{{ __('web/pages/place-request.messages.recaptcha_error') }}">

    {{-- JavaScript modules via Vite --}}
    @vite(['resources/js/web/place/place-request/index.js'])
@endpush

@section('content')
    <div class="min-h-screen bg-gray-50 py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- En-tête amélioré --}}
            <div class="mb-6 sm:mb-8">
                <div class="flex items-start gap-4">

                    {{-- Contenu --}}
                    <div class="flex-1">
                        {{-- Titre --}}
                        <h1 class="text-2xl sm:text-2xl font-medium text-gray-900 mb-2">
                            {{ __('web/pages/place-request.title') }}
                        </h1>

                        {{-- Subtitle --}}
                        <p class="text-sm sm:text-[1.1em] text-gray-700 font-normal mb-3">
                            {{ __('web/pages/place-request.subtitle') }}
                        </p>

                        {{-- Points clés en badges --}}
                        <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 text-sm font-medium rounded-md border border-blue-100">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ __('web/pages/place-request.badges.team_review') }}
                                </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 text-green-700 text-sm font-medium rounded-md border border-green-100">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                    {{ __('web/pages/place-request.badges.email_notification') }}
                                </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-purple-50 text-purple-700 text-sm font-medium rounded-md border border-purple-100">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ __('web/pages/place-request.badges.community_contribution') }}
                                </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Composant Livewire --}}
            <livewire:web.place.place-request.place-request-form />
        </div>
    </div>
@endsection

