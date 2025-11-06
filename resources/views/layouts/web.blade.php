@php
    // Données SEO par défaut si pas fournies par le contrôleur
    if (!isset($seo)) {
        $seoBuilderAction = app(\App\Domain\Seo\Actions\SeoBuilderAction::class);
        $seo = $seoBuilderAction->execute('homepage');
    }
    $alternates = $alternates ?? [];
    $breadcrumbs = $breadcrumbs ?? [];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="recaptcha-site-key" content="{{ config('recaptcha.site_key') }}">

    {{-- SEO complet via composants --}}
    <x-seo.head :seo="$seo" :alternates="$alternates" :breadcrumbs="$breadcrumbs" />
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google reCAPTCHA v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}" async defer></script>

    <!-- Additional Head Content -->
    @stack('head')
    @livewireStyles
</head>

<body class="antialiased bg-white text-gray-900 font-inter">
    <!-- Navbar -->
    <x-web.navbar />

    <!-- Flash Messages -->
    <x-web.flash-messages />

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    @if(isset($footer))
        <x-web.footer />
    @endif

    <!-- Error Modal -->
    <x-web.error-modal />

    <!-- Additional Scripts -->
    @stack('scripts')
    @livewireScripts
</body>
</html>
