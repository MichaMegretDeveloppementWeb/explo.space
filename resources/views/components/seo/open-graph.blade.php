@props(['seo'])

@php
    /** @var \App\Domain\Seo\DTO\SeoData $seo */
@endphp

{{-- Open Graph de base --}}
<meta property="og:title" content="{{ $seo->ogTitle }}">
<meta property="og:description" content="{{ $seo->ogDescription }}">
<meta property="og:type" content="{{ $seo->ogType }}">
<meta property="og:url" content="{{ $seo->ogUrl }}">
<meta property="og:site_name" content="{{ $seo->ogSiteName }}">
<meta property="og:locale" content="{{ $seo->ogLocale }}">

{{-- Image Open Graph --}}
@if($seo->ogImage)
    <meta property="og:image" content="{{ $seo->ogImage }}">
    <meta property="og:image:width" content="{{ config('seo.images.og_width', 1200) }}">
    <meta property="og:image:height" content="{{ config('seo.images.og_height', 630) }}">
    <meta property="og:image:type" content="image/jpeg">
    @if($seo->ogImageAlt)
        <meta property="og:image:alt" content="{{ $seo->ogImageAlt }}">
    @endif
@endif

{{-- Locales alternatives pour Open Graph --}}
@if(!empty($seo->ogLocaleAlternates))
    @foreach($seo->ogLocaleAlternates as $locale)
        <meta property="og:locale:alternate" content="{{ $locale }}">
    @endforeach
@endif

{{-- Métadonnées spécifiques selon le type --}}
@if($seo->ogType === 'place' || $seo->ogType === 'business.business')
    @if($seo->geo_latitude && $seo->geo_longitude)
        <meta property="place:location:latitude" content="{{ $seo->geo_latitude }}">
        <meta property="place:location:longitude" content="{{ $seo->geo_longitude }}">
    @endif
@endif

{{-- Article (si applicable) --}}
@if($seo->ogType === 'article')
    <meta property="article:author" content="{{ config('app.author') }}">
    <meta property="article:published_time" content="{{ now()->toISOString() }}">
    <meta property="article:section" content="Lieux spatiaux">
@endif
