@props(['seo'])

@php
    /** @var \App\Domain\Seo\DTO\SeoData $seo */
@endphp

{{-- Méta-données de base --}}
<title>{{ $seo->title }}</title>
<meta name="description" content="{{ $seo->description }}">
<meta name="keywords" content="{{ $seo->keywords }}">
<meta name="robots" content="{{ $seo->robots }}">
<meta name="author" content="{{ config('app.author') }}">

{{-- Géolocalisation pour les lieux --}}
@if($seo->geo_latitude && $seo->geo_longitude)
    <meta name="geo.position" content="{{ $seo->geo_latitude }};{{ $seo->geo_longitude }}">
    <meta name="geo.placename" content="{{ $seo->title }}">
    <meta name="ICBM" content="{{ $seo->geo_latitude }}, {{ $seo->geo_longitude }}">
@endif

{{-- Canonical --}}
<link rel="canonical" href="{{ $seo->canonical ?: url()->current() }}">

{{-- Preconnect pour performance --}}
<link rel="preconnect" href="https://fonts.bunny.net">
<link rel="dns-prefetch" href="//fonts.bunny.net">

{{-- Theme color --}}
<meta name="theme-color" content="#1E40AF">
<meta name="msapplication-navbutton-color" content="#1E40AF">
<meta name="apple-mobile-web-app-status-bar-style" content="default">

{{-- Sécurité --}}
<meta name="referrer" content="strict-origin-when-cross-origin">
