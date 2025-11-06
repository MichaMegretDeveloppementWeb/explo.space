@props(['seo'])

@php
    /** @var \App\Domain\Seo\DTO\SeoData $seo */
@endphp

{{-- Twitter Cards --}}
<meta name="twitter:card" content="{{ $seo->twitterCard }}">
<meta name="twitter:title" content="{{ $seo->twitterTitle }}">
<meta name="twitter:description" content="{{ $seo->twitterDescription }}">

{{-- Image Twitter --}}
@if($seo->twitterImage)
    <meta name="twitter:image" content="{{ $seo->twitterImage }}">
    @if($seo->twitterImageAlt)
        <meta name="twitter:image:alt" content="{{ $seo->twitterImageAlt }}">
    @endif
@endif

{{-- Compte Twitter du site (si configuré) --}}
@if($seo->twitterSite)
    <meta name="twitter:site" content="@{{ $seo->twitterSite }}">
@endif
@if($seo->twitterCreator)
    <meta name="twitter:creator" content="@{{ $seo->twitterCreator }}">
@endif

{{-- URL pour Twitter --}}
<meta name="twitter:url" content="{{ $seo->ogUrl }}">

{{-- Données spécifiques selon le type de carte --}}
@if($seo->twitterCard === 'summary_large_image')
    {{-- Optimisé pour grandes images --}}
    @if($seo->twitterImage)
        <meta name="twitter:image:width" content="{{ config('seo.images.twitter_width', 1200) }}">
        <meta name="twitter:image:height" content="{{ config('seo.images.twitter_height', 600) }}">
    @endif
@endif
