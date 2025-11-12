<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- IMPORTANT : Empêcher l'indexation des pages admin --}}
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', 'Administration - Explo.space')</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    {{-- Scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @stack('head')
</head>
<body class="antialiased bg-gray-50 font-inter">
    {{-- Navbar admin (visible seulement si connecté) --}}
    @auth
        @livewire('admin.navbar')
    @endauth

    {{-- Messages flash centralisés (Livewire) --}}
    @livewire('admin.flash-messages')

    {{-- Contenu principal --}}
    <main class="@auth mx-auto px-4 py-8 @endauth">
        @yield('content')
    </main>

    @livewireScripts
    @stack('scripts')
</body>
</html>
